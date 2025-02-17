<?php include 'header.php'; ?>
<link rel="stylesheet" type="text/css" href="games/game.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.5.0/fabric.min.js"></script>



<?php
if (isset($user_id)) {
    // Database connection
    $sql = "SELECT * FROM ".$siteprefix."game_levels WHERE id = 1";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $level = $row['id'];
        $title = $row['title'];
        $description = $row['description'];
        $java_code = $row['java_code'];
        $solution_code = $row['solution_code'];
        $hint = $row['hint'];
        }}
        else {
        // Default values if no data is found
        $level = 1;
        $title = "Default Level";
        $description = "Default Description";
        $java_code = "";
        $solution_code = "";
        $hint = "";
        }
    ?>

    <div class="row bg-dark">
    <div class="col-md-6 p-5 text-light">
        <h5 class="text-primary">Level <?php echo $level; ?>: <?php echo $title; ?></h5>
        <p><?php echo $description; ?><br>
        <pre class="text-primary"><code>Java Code: <?php echo htmlspecialchars($java_code); ?></code></pre>
        Hint: <?php echo $hint; ?></p>


<div id="editorContainer" class="mt-4">
<textarea id="javaEditor" rows="10" cols="40"></textarea><br>
<button class="btn btn-primary w-100" onclick="executeJavaCode()">Run Code</button>
</div>
</div>
<div class="col-md-6">
<canvas id="tetrisCanvas" width="300" height="600"></canvas>
</div>
</div>


<script>
    // Initialize CodeMirror for Incomplete Code
const incompleteCodeEditor = CodeMirror.fromTextArea(document.getElementById('javaEditor'), {
        lineNumbers: true,
        mode: 'text/x-java', // Default mode
        theme: 'dracula',
});

// Function to execute Java code    
const canvas = document.getElementById("tetrisCanvas");
const ctx = canvas.getContext("2d");
const ROWS = 20;
const COLUMNS = 10;
const BLOCK_SIZE = 30;
const COLORS = ["red", "green", "blue", "yellow", "purple", "cyan", "orange"];

const TETROMINOS = [
  [[1, 1, 1, 1]], // I shape
  [[1, 1], [1, 1]], // O shape
  [[0, 1, 0], [1, 1, 1]], // T shape
  [[1, 1, 0], [0, 1, 1]], // Z shape
  [[0, 1, 1], [1, 1, 0]], // S shape
  [[1, 0, 0], [1, 1, 1]], // L shape
  [[0, 0, 1], [1, 1, 1]], // J shape
];

let grid = Array.from({ length: ROWS }, () => Array(COLUMNS).fill(0));
let currentPiece;
let gameRunning = true;

/** Generate a new Tetromino */
function generatePiece() {
  const randomIndex = Math.floor(Math.random() * TETROMINOS.length);
  return {
    shape: TETROMINOS[randomIndex],
    color: COLORS[randomIndex],
    row: 0,
    col: Math.floor(COLUMNS / 2) - 1,
  };
}

/** Draw the grid and blocks */
function draw() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  // Draw the grid
  for (let r = 0; r < ROWS; r++) {
    for (let c = 0; c < COLUMNS; c++) {
      if (grid[r][c]) {
        ctx.fillStyle = grid[r][c];
        ctx.fillRect(c * BLOCK_SIZE, r * BLOCK_SIZE, BLOCK_SIZE, BLOCK_SIZE);
        ctx.strokeRect(c * BLOCK_SIZE, r * BLOCK_SIZE, BLOCK_SIZE, BLOCK_SIZE);
      }
    }
  }

  // Draw current piece
  if (currentPiece) {
    ctx.fillStyle = currentPiece.color;
    currentPiece.shape.forEach((row, r) => {
      row.forEach((cell, c) => {
        if (cell) {
          ctx.fillRect(
            (currentPiece.col + c) * BLOCK_SIZE,
            (currentPiece.row + r) * BLOCK_SIZE,
            BLOCK_SIZE,
            BLOCK_SIZE
          );
          ctx.strokeRect(
            (currentPiece.col + c) * BLOCK_SIZE,
            (currentPiece.row + r) * BLOCK_SIZE,
            BLOCK_SIZE,
            BLOCK_SIZE
          );
        }
      });
    });
  }
}

/** Move the piece down */
function moveDown() {
  if (!collision(1, 0)) {
    currentPiece.row++;
  } else {
    mergePiece();
    currentPiece = generatePiece();
    if (collision(0, 0)) {
      alert("Game Over");
      gameRunning = false;
    }
  }
  draw();
}

/** Check collision */
function collision(rowOffset, colOffset) {
  return currentPiece.shape.some((row, r) =>
    row.some(
      (cell, c) =>
        cell &&
        (grid[currentPiece.row + r + rowOffset]?.[currentPiece.col + c + colOffset] !==
          0 || currentPiece.row + r + rowOffset >= ROWS)
    )
  );
}

/** Merge current piece into the grid */
function mergePiece() {
  currentPiece.shape.forEach((row, r) => {
    row.forEach((cell, c) => {
      if (cell) {
        grid[currentPiece.row + r][currentPiece.col + c] = currentPiece.color;
      }
    });
  });
  checkLines();
}

/** Check for complete lines */
function checkLines() {
  grid = grid.filter(row => row.some(cell => cell === 0));
  while (grid.length < ROWS) {
    grid.unshift(Array(COLUMNS).fill(0));
  }
}

/** Move piece left */
function moveLeft() {
  if (!collision(0, -1)) currentPiece.col--;
  draw();
}


function moveRight() { 
    console.log("Moving Right");
    if (!collision(0, 1)) currentPiece.col++;
    draw();
    
    // Ensure this function is defined in the game engine
    if (typeof game !== "undefined" && typeof game.moveBlock === "function") {
        game.moveBlock("right");
    } else {
        console.error("Game engine not found or moveBlock function missing.");
    }
}

/** Rotate the piece */
function rotate() {
  const rotated = currentPiece.shape[0].map((_, c) =>
    currentPiece.shape.map(row => row[c]).reverse()
  );
  if (!collision(0, 0)) currentPiece.shape = rotated;
  draw();
}

function parseJavaCode(javaCode) {
    try {
        javaCode = javaCode.trim();
        console.log("Original Java Code:", javaCode);

        // Convert Java methods (public void moveRight() {...}) to JavaScript functions
        javaCode = javaCode.replace(/public void (\w+)\(\)\s*{/, "function $1() {");
        console.log("After method conversion:", javaCode);

        // Convert Java print statements to JavaScript console logs
        javaCode = javaCode.replace(/System\.out\.println\((.*)\);/g, 'console.log($1);');
        console.log("After print statement conversion:", javaCode);

        // Convert Java function calls
        const commandMap = {
            "block.moveLeft();": "moveLeft();",
            "block.moveRight();": "moveRight();",
            "block.moveDown();": "moveDown();",
            "block.rotate();": "rotate();"
        };

        Object.keys(commandMap).forEach(javaCmd => {
            javaCode = javaCode.replace(new RegExp(javaCmd.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), "g"), commandMap[javaCmd]);
        });
        console.log("After command conversion:", javaCode);

        // Execute JavaScript code
        const script = document.createElement('script');
        script.textContent = javaCode;
        document.body.appendChild(script);
        document.body.removeChild(script);

    } catch (error) {
        alert("Error in Java code: " + error.message);
        console.error("Error in Java code:", error);
    }
}


/** Keyboard controls */
document.addEventListener("keydown", (event) => {
  if (!gameRunning) return;
  switch (event.key) {
    case "ArrowLeft":
      moveLeft();
      break;
    case "ArrowRight":
      moveRight();
      break;
    case "ArrowDown":
      moveDown();
      break;
    case " ":
      rotate();
      break;
  }
});

/** Start the game */
function startGame() {
  currentPiece = generatePiece();
  setInterval(() => {
    if (gameRunning) moveDown();
  }, 1000);
  draw();
}

startGame();


// Function to execute Java code
function executeJavaCode() {
    const javaCode = incompleteCodeEditor.getValue();
    parseJavaCode(javaCode);
    console.log("Executing Java code...");
}

// Function to display a modal
function displayModal(message, buttonText, callback) {
    const modal = document.createElement("div");
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.backgroundColor = "rgba(0, 0, 0, 0.8)";
    modal.style.display = "flex";
    modal.style.justifyContent = "center";
    modal.style.alignItems = "center";
    modal.style.zIndex = "1000";

    const modalContent = document.createElement("div");
    modalContent.style.backgroundColor = "white";
    modalContent.style.padding = "20px";
    modalContent.style.borderRadius = "5px";
    modalContent.style.textAlign = "center";

    const modalMessage = document.createElement("p");
    modalMessage.innerText = message;

    const modalButton = document.createElement("button");
    modalButton.innerText = buttonText;
    modalButton.style.marginTop = "10px";
    modalButton.onclick = () => {
        document.body.removeChild(modal);
        callback();
    };

    modalContent.appendChild(modalMessage);
    modalContent.appendChild(modalButton);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
}

// Function to send data to PHP
function sendGameResult(status) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "save_game_result.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    let params = `level=${currentLevel}&status=${status}&clearedRows=${clearedRows}`;
    xhr.send(params);
}
</script>
</script>
<?php include 'footer.php'; ?>