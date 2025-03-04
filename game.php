<?php include 'header.php'; ?>
<link rel="stylesheet" type="text/css" href="games/game.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.5.0/fabric.min.js"></script>


<?php
    // Database connection
    if ($active_log==1){
      $sql = "SELECT MAX(level) as last_level FROM ".$siteprefix."game_progress 
      WHERE user_id = ? AND status = 'completed'";
      $stmt = $con->prepare($sql);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      
      if (isset($_GET['level']) && $row['last_level'] >= 40) {
      $current_level = intval($_GET['level']);
      } else if ($row['last_level'] >= 40) {
      // Get all levels for dropdown
      $levels_sql = "SELECT id, title FROM ".$siteprefix."game_levels ORDER BY id";
      $levels_result = $con->query($levels_sql);
      
      echo '<div id="completionModal" class="modal">
        <div class="modal-content">
        <h3>Congratulations!</h3>
        <p>You have completed all game levels! Select any level to review:</p>
        <select id="levelSelect" onchange="window.location.href=\'game.php?level=\'+this.value">';
      while($level = $levels_result->fetch_assoc()) {
        echo '<option value="'.$level['id'].'">Level '.$level['id'].': '.$level['title'].'</option>';
      }
      echo '</select>
        </div>
      </div>';
      echo '<script>
        document.getElementById("completionModal").style.display = "block";
      </script>';
      $current_level = 1;
      } else {
      $current_level = ($row['last_level']) ? $row['last_level'] + 1 : 1;
      }
    } else {
      $current_level = 1; 
    }

    // Get level details
    $sql = "SELECT * FROM ".$siteprefix."game_levels WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $current_level);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $level = $row['id'];
      $title = $row['title'];
      $description = $row['description'];
      $java_code = $row['java_code'];
      $solution_code = $row['solution_code'];
      $hint = $row['hint'];
    }
    ?>

   <div class="container">
   <div class="row bg-dark justify-content-center">
  <div class="col-md-6 p-5 text-light">
        <h5 class="text-primary" id="levelTitle">Level <?php echo $level; ?>: <?php echo $title; ?></h5>
        <p id="levelDescription"><?php echo $description; ?><br>
        <pre class="text-primary"><code>Java Code: <?php echo htmlspecialchars($java_code); ?></code></pre>
        Hint: <?php echo $hint; ?></p>


<div id="editorContainer" class="mt-4">
<textarea id="javaEditor" rows="10" cols="40"></textarea><br>
<button class="btn btn-primary w-100" id="RunButton" onclick="executeJavaCode()">Run Code</button>
</div>
</div>
<div class="col-md-6">
<canvas id="tetrisCanvas" width="600" height="600"></canvas>
</div>
</div>


<script>
// Initialize CodeMirror for Incomplete Code
const incompleteCodeEditor = CodeMirror.fromTextArea(document.getElementById('javaEditor'), {
    lineNumbers: true,
    mode: 'text/x-java', // Default mode
    theme: 'dracula',
});

// Game variables
const canvas = document.getElementById("tetrisCanvas");
const ctx = canvas.getContext("2d");
const ROWS = 20;
const COLUMNS = 10;
let BLOCK_SIZE = 30;
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
let currentLevel = <?php echo $level; ?>;
let clearedRows = 0;
let gameOn = true; // Added for level compatibility
let xPos = 0;
let yPos = 0;
let maxY = ROWS - 1;

// Create game object for level compatibility
const game = {
    moveBlock: function(direction) {
        if (direction === "left") {
            moveLeft();
        } else if (direction === "right") {
            moveRight();
        } else if (direction === "down") {
            moveDown();
        }
    },
    cleanup: function() {
        //console.log("Game resources cleaned up");
    },
    close: function() {
        //console.log("Game closed");
    }
};

/** Generate a new Tetromino */
function generatePiece() {
  const randomIndex = Math.floor(Math.random() * TETROMINOS.length);
  const piece = {
    shape: TETROMINOS[randomIndex],
    color: COLORS[randomIndex],
    row: 0,
    col: Math.floor(COLUMNS / 2) - 1,
  };
  // Update xPos and yPos for level compatibility
  xPos = piece.col;
  yPos = piece.row;
  return piece;
}

function adjustForMobile() {
  // Check if device is mobile (screen width less than 768px)
  if (window.innerWidth < 768) {
    // Adjust textarea rows
    document.getElementById('javaEditor').rows = 5;
    
    // Adjust canvas height and block size for mobile
    const canvas = document.getElementById('tetrisCanvas');
    canvas.height = 400;
    BLOCK_SIZE = canvas.height / ROWS; // Adjust block size based on new height
  }
}
window.addEventListener('resize', adjustForMobile);
window.addEventListener('load', adjustForMobile);

/** Draw the grid and blocks */
function draw() {
  // Clear the entire canvas
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  
  // Calculate block size based on canvas dimensions
  const blockSize = canvas.height / ROWS;

  // Draw the grid
  for (let r = 0; r < ROWS; r++) {
    for (let c = 0; c < COLUMNS; c++) {
      if (grid[r][c]) {
        ctx.fillStyle = grid[r][c];
        ctx.fillRect(c * blockSize, r * blockSize, blockSize, blockSize);
        ctx.strokeRect(c * blockSize, r * blockSize, blockSize, blockSize);
      }
    }
  }

  // Draw current piece with adjusted block size
  if (currentPiece) {
    ctx.fillStyle = currentPiece.color;
    currentPiece.shape.forEach((row, r) => {
      row.forEach((cell, c) => {
        if (cell) {
          ctx.fillRect(
            (currentPiece.col + c) * blockSize,
            (currentPiece.row + r) * blockSize,
            blockSize,
            blockSize
          );
          ctx.strokeRect(
            (currentPiece.col + c) * blockSize,
            (currentPiece.row + r) * blockSize,
            blockSize,
            blockSize
          );
        }
      });
    });
  }
}

/** Move the piece down */
function moveDown() {
  if (!gameRunning || !gameOn) return;
  
  if (!collision(1, 0)) {
    currentPiece.row++;
    yPos++; // Update yPos for level compatibility
  } else {
    mergePiece();
    currentPiece = generatePiece();
    if (collision(0, 0)) {
      displayModal("Game Over", "Try Again", function() {
        resetGame();
      });
      gameRunning = false;
      gameOn = false;
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
        (
          // Check if it's outside grid bounds or colliding with existing blocks
          currentPiece.col + c + colOffset < 0 ||
          currentPiece.col + c + colOffset >= COLUMNS ||
          currentPiece.row + r + rowOffset >= ROWS ||
          (grid[currentPiece.row + r + rowOffset] && 
           grid[currentPiece.row + r + rowOffset][currentPiece.col + c + colOffset] !== 0)
        )
    )
  );
}

/** Merge current piece into the grid */
function mergePiece() {
  currentPiece.shape.forEach((row, r) => {
    row.forEach((cell, c) => {
      if (cell) {
        if (currentPiece.row + r >= 0 && currentPiece.col + c >= 0) {
          grid[currentPiece.row + r][currentPiece.col + c] = currentPiece.color;
        }
      }
    });
  });
  checkLines();
}

/** Check for complete lines */
function checkLines() {
  let rowsCleared = 0;
  for (let r = ROWS - 1; r >= 0; r--) {
    if (grid[r].every(cell => cell !== 0)) {
      grid.splice(r, 1);
      grid.unshift(Array(COLUMNS).fill(0));
      rowsCleared++;
      clearedRows++;
    }
  }
}

/** Move piece left */
function moveLeft() {
  if (!gameRunning || !gameOn) return;
  
  if (!collision(0, -1)) {
    currentPiece.col--;
    xPos--; // Update xPos for level compatibility
  }
  draw();
}

/** Move piece right */
function moveRight() { 
  if (!gameRunning || !gameOn) return;
  
  //console.log("Moving Right");
  if (!collision(0, 1)) {
    currentPiece.col++;
    xPos++; // Update xPos for level compatibility
  }
  draw();
}

/** Rotate the piece */
function rotate() {
  if (!gameRunning || !gameOn) return;
  
  const rotated = currentPiece.shape[0].map((_, c) =>
    currentPiece.shape.map(row => row[c]).reverse()
  );
  
  // Save current shape
  const originalShape = [...currentPiece.shape];
  
  // Try rotation
  currentPiece.shape = rotated;
  
  // If rotation causes collision, revert back
  if (collision(0, 0)) {
    currentPiece.shape = originalShape;
  }
  
  draw();
}

/** Helper function to play sounds (placeholder) */
function playSound(type) {
  const sounds = {
    move: new Audio('sounds/game.wav'),
    rotate: new Audio('sounds/game.wav'),
    drop: new Audio('sounds/game.wav'),
    clear: new Audio('sounds/game.wav')
  };

  if (sounds[type]) {
    sounds[type].currentTime = 0;
    sounds[type].play().catch(error => {
      // Silently handle autoplay restrictions
      //console.log("Sound playback error:", error);
    });
  }
}

/** Define a method to move a block in a specific direction */
function moveBlock(direction) {
  if (direction === "left") {
    moveLeft();
  } else if (direction === "right") {
    moveRight();
  } else if (direction === "down") {
    moveDown();
  }
}

/** Reset the game */
function resetGame() {
  grid = Array.from({ length: ROWS }, () => Array(COLUMNS).fill(0));
  currentPiece = generatePiece();
  gameRunning = true;
  gameOn = true;
  clearedRows = 0;
  draw();
}

/** Parse Java code and convert to JavaScript */
function parseJavaCode(javaCode) {
  playSound("move");
    try {
        javaCode = javaCode.trim();
        //console.log("Original Java Code:", javaCode);

        // Convert Java methods to JavaScript functions
        // Fix for missing void
        javaCode = javaCode.replace(/public\s+(\w+)\(\)/, "public void $1()");
        javaCode = javaCode.replace(/public\s+void\s+(\w+)\s*\(\)\s*{/, "function $1() {");
        javaCode = javaCode.replace(/public\s+void\s+(\w+)\s*\(([^)]*)\)\s*{/, "function $1($2) {");
        
        // Handle class definitions by extracting the methods
        javaCode = javaCode.replace(/class\s+(\w+)\s*{([\s\S]*)}/, function(match, className, classContent) {
            // Extract methods from class content and return just the methods
            return classContent.trim();
        });
        
        //console.log("After method conversion:", javaCode);

        // Convert Java print statements to JavaScript console logs
        javaCode = javaCode.replace(/System\.out\.println\((.*?)\);/g, '//console.log($1);');
        //console.log("After print statement conversion:", javaCode);

        // Add a unique prefix to the function name
        javaCode = javaCode.replace(/function\s+(\w+)\s*\((.*?)\)\s*{/g, function(match, funcName, params) {
            // Store the function name in a variable to use later
            let lastFuncName = funcName;
            return `function user_${funcName}(${params}) {`;
        });

        // Convert Java function calls
        const commandMap = {
            "block.moveLeft()": "moveLeft()",
            "block.moveRight()": "moveRight()",
            "block.moveDown()": "moveDown()",
            "block.rotate()": "rotate()",
            "game.moveBlock(\"left\")": "moveBlock(\"left\")",
            "game.moveBlock(\"right\")": "moveBlock(\"right\")",
            "game.moveBlock(\"down\")": "moveBlock(\"down\")"
        };

        Object.keys(commandMap).forEach(javaCmd => {
            javaCode = javaCode.replace(new RegExp(javaCmd.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), "g"), commandMap[javaCmd]);
        });
        //console.log("After command conversion:", javaCode);

        // Extract the function name from the code
        const funcNameMatch = javaCode.match(/function\s+user_(\w+)/);
        const functionToCall = funcNameMatch ? funcNameMatch[1] : null;

        // Add function call after declaration if we found a function name
        if (functionToCall) {
            javaCode += `\nuser_${functionToCall}();`;
        }
        //console.log("Final JavaScript code:", javaCode);

        // Execute JavaScript code
        const script = document.createElement('script');
        script.type = 'text/javascript';
        // Wrap the code in a try-catch block and IIFE
        script.textContent = `(function() { try { ${javaCode} } catch(e) { console.error(e); } })();`;
        // Use a safe way to append and execute script
        (document.head || document.documentElement).appendChild(script);
        script.remove();

        return true;
    } catch (error) {
        alert("Error in Java code: " + error.message);
        console.error("Error in Java code:", error);
        return false;
    }
}

/** Execute Java code and verify against solution */
function executeJavaCode() {
  const javaCode = incompleteCodeEditor.getValue();
  const success = parseJavaCode(javaCode);
  const active_log = <?php echo $active_log; ?>;
  const user = <?php echo (isset($user_id) && !empty($user_id)) ? $user_id : 'null'; ?>;
  const game_level = currentLevel;
  const solution = <?php echo json_encode($solution_code); ?>;
  
  // Disable button and change text
  const runButton = document.getElementById('RunButton');
  runButton.disabled = true;
  runButton.textContent = 'Executing code...';

  if (success) {
    // Check if code matches solution
    if (javaCode.replace(/\s+/g, '') === solution.replace(/\s+/g, '')) {
      if (active_log == 1 && user && game_level) {
        // Log game progress
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "log_game_progress.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(`user_id=${user}&level=${game_level}&status=completed`);
        
    
          xhr.onload = function() {
            // Re-enable button and restore text
            runButton.disabled = false;
            runButton.textContent = 'Run Code';

            const response = xhr.responseText;
            if (response.includes("Code executed successfully")) {
              const nextLevel = parseInt(response.match(/level (\d+)/)[1]);
                displayModal(response, "Continue",null);
                setTimeout(() => {
                  window.location.reload();
                }, 3000);
              
            } else if (response.includes("Error saving progress")) {
              displayModal("Error saving progress", "Try Again", null);
            } else if (response.includes("Invalid data")) {
              displayModal("Invalid data submitted", "Try Again", null);
            }
          };
      } else {
        runButton.disabled = false;
        runButton.textContent = 'Run Code';
        window.location.href = 'signin.php';
      }
    } else {
      runButton.disabled = false;
      runButton.textContent = 'Run Code';
      displayModal("Code compiled successfully. keep goin to solve this level!", "OK", null);
    }
  } else {
    runButton.disabled = false;
    runButton.textContent = 'Run Code';
  }
}



/** Function to display a modal */
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
    modalButton.className = "btn-get-started";
    modalButton.onclick = () => {
      document.body.removeChild(modal);
      if (callback) callback();
    };


    modalContent.appendChild(modalMessage);
    modalContent.appendChild(modalButton);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
}

/** Function to send data to PHP */
function sendGameResult(status) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "save_game_result.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    let params = `level=${currentLevel}&status=${status}&clearedRows=${clearedRows}`;
    xhr.send(params);
}

/** Keyboard and mouse controls */
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

/**on page load */
document.addEventListener('DOMContentLoaded', function() {
  // Hide footer
  const footer = document.getElementById('footer');
  if (footer) {
    footer.style.display = 'none';
  }
});
// Mouse controls
canvas.addEventListener("click", (event) => {
  if (!gameRunning) return;
  
  const rect = canvas.getBoundingClientRect();
  const x = event.clientX - rect.left;
  const canvasWidth = rect.width;
  
  if (x < canvasWidth / 3) {
    moveLeft();
  } else if (x > (canvasWidth * 2) / 3) {
    moveRight();
  } else {
    rotate();
  }
});

canvas.addEventListener("contextmenu", (event) => {
  if (!gameRunning) return;
  event.preventDefault();
  moveDown();
});

/** Start the game */
function startGame() {
  currentPiece = generatePiece();
  playSound("move");
  setInterval(() => {
    if (gameRunning) moveDown();
  }, 5000);
  draw();
  
  // Load first level
  loadLevel(<?php echo $current_level;?>);
}

// Start the game when the page loads
document.addEventListener('DOMContentLoaded', function() {
    startGame();
});

/** Load a level from database */
function loadLevel(levelId) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "get_game_level.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send(`level_id=${levelId}`);

  xhr.onload = function() {
    if (xhr.status === 200) {
      const level = JSON.parse(xhr.responseText);
      if (level) {
        document.getElementById('levelTitle').textContent = `Level ${level.id}: ${level.title}`;
        document.getElementById('levelDescription').textContent = level.description;
        incompleteCodeEditor.setValue(level.java_code);
        currentLevel = levelId;
      }
    }
  };
}

// Add a function to show hints for current level
function showHint() {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "get_game_hint.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send(`level=${currentLevel}`);

  xhr.onload = function() {
    if (xhr.status === 200) {
      const response = JSON.parse(xhr.responseText);
      const hint = response.hint || "No hint available for this level.";
      displayModal("Hint: " + hint, "Got it!", null);
    }
  };
}

showHint();
</script>
<?php include 'footer.php'; ?>