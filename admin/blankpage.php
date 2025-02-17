<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radio Button Form</title>
    <style>
        .radio-container {
            background-color: grey;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }
        .radio-container input[type="radio"]:checked + label {
            color: blue;
        }
        .radio-container label {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <form>
        <div class="radio-container">
            <input type="radio" id="option1" name="options" value="1" checked>
            <label for="option1">Option 1</label>
        </div>
        <div class="radio-container">
            <input type="radio" id="option2" name="options" value="2">
            <label for="option2">Option 2</label>
        </div>
    </form>
</body>
</html>