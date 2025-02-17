const canvas = document.getElementById("tetrisCanvas");
const ctx = canvas.getContext("2d");
const ROWS = 20;
const COLS = 10;
const BLOCK_SIZE = 30;
const board = Array.from({ length: ROWS }, () => Array(COLS).fill(0));

const tetrominoes = {
    I: [[1, 1, 1, 1]],
    O: [[1, 1], [1, 1]],
    T: [[0, 1, 0], [1, 1, 1]],
    L: [[1, 0], [1, 0], [1, 1]],
    J: [[0, 1], [0, 1], [1, 1]],
    S: [[0, 1, 1], [1, 1, 0]],
    Z: [[1, 1, 0], [0, 1, 1]]
};

let currentPiece = {
    shape: tetrominoes["T"],
    row: 0,
    col: 3
};

function drawBlock(x, y, color = "blue") {
    ctx.fillStyle = color;
    ctx.fillRect(x * BLOCK_SIZE, y * BLOCK_SIZE, BLOCK_SIZE, BLOCK_SIZE);
    ctx.strokeStyle = "black";
    ctx.strokeRect(x * BLOCK_SIZE, y * BLOCK_SIZE, BLOCK_SIZE, BLOCK_SIZE);
}

function drawBoard() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    for (let r = 0; r < ROWS; r++) {
        for (let c = 0; c < COLS; c++) {
            if (board[r][c]) drawBlock(c, r, "gray");
        }
    }
    
    currentPiece.shape.forEach((row, rIdx) => {
        row.forEach((val, cIdx) => {
            if (val) drawBlock(currentPiece.col + cIdx, currentPiece.row + rIdx, "red");
        });
    });
}

function moveDown() {
    currentPiece.row++;
    drawBoard();
}

function moveUp() {
    if (currentPiece.row > 0) currentPiece.row--;
    drawBoard();
}

function moveLeft() {
    if (currentPiece.col > 0) currentPiece.col--;
    drawBoard();
}

function moveRight() {
    if (currentPiece.col < COLS - currentPiece.shape[0].length) currentPiece.col++;
    drawBoard();
}

function jumpTwoSpaces() {
    currentPiece.row = Math.max(0, currentPiece.row - 2);
    drawBoard();
}

function moveTwoRight() {
    if (currentPiece.col < COLS - currentPiece.shape[0].length - 1) currentPiece.col += 2;
    drawBoard();
}

function moveTwoLeft() {
    if (currentPiece.col > 1) currentPiece.col -= 2;
    drawBoard();
}

function rotateCurrentPiece() {
    let rotated = currentPiece.shape[0].map((_, index) => currentPiece.shape.map(row => row[index]).reverse());
    currentPiece.shape = rotated;
    drawBoard();
}

function specialMove() {
    moveRight();
    jumpTwoSpaces();
    rotatePiece();
}

setInterval(moveDown, 500); // Drop block every 500ms

document.addEventListener("keydown", (event) => {
    if (event.key === "ArrowDown") moveDown();
    if (event.key === "ArrowUp") moveUp();
    if (event.key === "ArrowLeft") moveLeft();
    if (event.key === "ArrowRight") moveRight();
    if (event.key === " ") jumpTwoSpaces();
    if (event.key === "d") moveTwoRight();
    if (event.key === "a") moveTwoLeft();
    if (event.key === "r") rotateCurrentPiece();
});

drawBoard();

// Java Code Parser
const javaToJsMap = {
    "public void moveUp() { }": "moveUp();",
    "public void moveDown() { }": "moveDown();",
    "public void moveLeft() { }": "moveLeft();",
    "public void moveRight() { }": "moveRight();",
    "public void jumpTwoSpaces() { }": "jumpTwoSpaces();",
    "public void moveTwoRight() { }": "moveTwoRight();",
    "public void moveTwoLeft() { }": "moveTwoLeft();",
    "public void rotatePiece() { }": "rotateCurrentPiece();",
    "public void specialMove() { }": "specialMove();"
};

function executeJavaCode(javaCode) {
    try {
        const matches = javaCode.match(/public void (\w+)\(\) \{ \}/g);
        if (matches) {
            matches.forEach(cmd => {
                const jsCommand = javaToJsMap[cmd.trim()];
                if (jsCommand) eval(jsCommand);
                else console.error("Invalid Java method: " + cmd);
            });
        } else {
            console.error("No valid Java functions found.");
        }
    } catch (error) {
        console.error("Error executing Java code:", error);
    }
}

// Java Code Parser - Converts Java Methods to JavaScript Actions
function parseJavaCode(javaCode) {
    try {
        // Error handling: Check for syntax errors
        if (!javaCode.includes("public void")) {
            throw new Error("Invalid Java method declaration. Use 'public void methodName() { }'");
        }
        
        // Extract method name and parameters
        const match = javaCode.match(/public void (\w+)\((.*?)\) \{/);
        if (!match) {
            throw new Error("Could not parse method. Ensure correct syntax.");
        }
        
        const methodName = match[1];
        const params = match[2].split(',').map(param => param.trim());
        
        // Define Java to JavaScript function mapping
        const actions = {
            moveRight: (steps = 1) => movePiece('right', steps),
            moveLeft: (steps = 1) => movePiece('left', steps),
            moveDown: (steps = 1) => movePiece('down', steps),
            jumpTwoSpaces: () => movePiece('up', 2),
            flipPiece: () => rotatePiece(),
            specialMove: () => { movePiece('right', 2); movePiece('down', 1); rotatePiece(); }
        };
        
        // Execute function if it exists
        if (actions[methodName]) {
            if (params.length > 0 && !isNaN(params[0])) {
                actions[methodName](parseInt(params[0]));
            } else {
                actions[methodName]();
            }
        } else {
            throw new Error(`Unknown method '${methodName}'. Available functions: ${Object.keys(actions).join(", ")}`);
        }
    } catch (error) {
        displayError(error.message);
    }
}

// Function to move a Tetris piece
function movePiece(direction, steps) {
    console.log(`Moving ${direction} by ${steps} step(s).`);
    // JS logic to move the Tetris piece visually
}

// Function to rotate the Tetris piece
function rotatePiece() {
    console.log("Rotating piece.");
    // JS logic for rotation
}

// Function to display errors
function displayError(message) {
    console.error("Java Code Error: ", message);
    alert(`Error: ${message}`);
}

// Example Usage
const userJavaCode = "public void moveRight(3) { }";
parseJavaCode(userJavaCode);


// Text Editor to Input Java Code
const editor = document.createElement("textarea");
editor.id = "javaEditor";
editor.rows = 10;
editor.cols = 40;
document.body.appendChild(editor);

const runButton = document.createElement("button");
runButton.innerText = "Run Code";
runButton.onclick = () => {
    const code = document.getElementById("javaEditor").value;
    executeJavaCode(code);
};
document.body.appendChild(runButton);


let currentLevel = 1; // Track level
let requiredRows = 0; // Will be set per level
let clearedRows = 0; // Count rows cleared
let gameOver = false;

// Function to check if player wins
function checkWin() {
    if (clearedRows >= requiredRows) {
        alert("Congratulations! You won level " + currentLevel);
        sendGameResult('win'); // Save win in database
    }
}

// Function to check if player loses
function checkLoss(grid) {
    // Check if top row is filled
    let topRow = grid[0]; // Assuming grid[0] is the topmost row
    if (topRow.some(cell => cell !== 0)) { // If any block exists
        alert("Game Over! You lost level " + currentLevel);
        sendGameResult('loss'); // Save loss in database
        gameOver = true;
    }
}

// Function to send data to PHP
function sendGameResult(status) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "save_game_result.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    let params = `level=${currentLevel}&status=${status}&clearedRows=${clearedRows}`;
    xhr.send(params);
}
