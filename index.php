<?php

function populateMatrix() {
    $index = 1;
    $matrix = [];
    
    for ($x = 0;$x <= 8;$x++) {
        for ($y = 0;$y <= 8;$y++) {
            $matrix[$x][$y] = "&nbsp;";
            $index++;
        }
    }
    
    return $matrix;
}

function createInitialScheme($difficulty = "medium") {
    $matrix = populateMatrix();
    solveSudoku($matrix);
    shuffleSolvedSudoku($matrix);
    $cellsToKeep = getDifficultyLevel($difficulty);
    $cellsToRemove = 81 - $cellsToKeep;
    
    while ($cellsToRemove > 0) {
        $row = rand(0, 8);
        $col = rand(0, 8);
        if ($matrix[$row][$col] != "&nbsp;") {
            $matrix[$row][$col] = "&nbsp;";
            $cellsToRemove--;
        }
    }
    
    return $matrix;
}

function getDifficultyLevel($difficulty) {
    switch ($difficulty) {
        case "easy":
            return 35;
        case "medium":
            return 30;
        case "hard":
            return 25;
        case "expert":
            return 20;
        default:
            return 30; // Default to medium
            
    }
}

function shuffleSolvedSudoku(&$matrix) {
    // Shuffle rows within each block
    for ($block = 0;$block < 3;$block++) {
        $rowStart = $block * 3;
        for ($i = 0;$i < 3;$i++) {
            $row1 = $rowStart + rand(0, 2);
            $row2 = $rowStart + rand(0, 2);
            for ($col = 0;$col < 9;$col++) {
                $temp = $matrix[$row1][$col];
                $matrix[$row1][$col] = $matrix[$row2][$col];
                $matrix[$row2][$col] = $temp;
            }
        }
    }
    
    // Shuffle columns within each block
    for ($block = 0;$block < 3;$block++) {
        $colStart = $block * 3;
        
        for ($i = 0;$i < 3;$i++) {
            $col1 = $colStart + rand(0, 2);
            $col2 = $colStart + rand(0, 2);
            
            for ($row = 0;$row < 9;$row++) {
                $temp = $matrix[$row][$col1];
                $matrix[$row][$col1] = $matrix[$row][$col2];
                $matrix[$row][$col2] = $temp;
            }
        }
    }
    
    // Shuffle row blocks
    for ($i = 0;$i < 3;$i++) {
        $block1 = rand(0, 2);
        $block2 = rand(0, 2);
        
        for ($row = 0;$row < 3;$row++) {
            $tempRow = $matrix[$block1 * 3 + $row];
            $matrix[$block1 * 3 + $row] = $matrix[$block2 * 3 + $row];
            $matrix[$block2 * 3 + $row] = $tempRow;
        }
    }
    // Shuffle column blocks
    for ($i = 0;$i < 3;$i++) {
        $block1 = rand(0, 2);
        $block2 = rand(0, 2);
        
        for ($col = 0;$col < 3;$col++) {
            for ($row = 0;$row < 9;$row++) {
                $temp = $matrix[$row][$block1 * 3 + $col];
                $matrix[$row][$block1 * 3 + $col] = $matrix[$row][$block2 * 3 + $col];
                $matrix[$row][$block2 * 3 + $col] = $temp;
            }
        }
    }
}

function solveSudoku(&$matrix) {
    $emptyCell = findEmptyCell($matrix);
    
    if (!$emptyCell) {
        return true;
    }
    
    list($row, $col) = $emptyCell;
    
    for ($num = 1;$num <= 9;$num++) {
        if (checkValidity($matrix, $row, $col, $num)) {
            $matrix[$row][$col] = $num;
            if (solveSudoku($matrix)) {
                return true;
            }
            $matrix[$row][$col] = "&nbsp;";
        }
    }
    
    return false;
}

function isSolvable($matrix) {
    $tempMatrix = $matrix;
    return solveSudoku($tempMatrix);
}

function checkValidity($matrix, $col, $row, $value) {
    $status = true;
    
    // check value in column
    for ($y = 0;$y <= 8;$y++) {
        if ($y == $col) {
            for ($x = 0;$x <= 8;$x++) {
                if ($matrix[$y][$x] == $value) {
                    $status = false;
                }
            }
        }
    }
    
    // check value in row
    for ($y = 0;$y <= 8;$y++) {
        for ($x = 0;$x <= 8;$x++) {
            if ($x == $row) {
                if ($matrix[$y][$x] == $value) {
                    $status = false;
                }
            }
        }
    }
    
    // check value in square
    $values = determineSquare($col, $row);
    for ($y = $values["fromY"];$y <= $values["toY"];$y++) {
        for ($x = $values["fromX"];$x <= $values["toX"];$x++) {
            if ($matrix[$y][$x] == $value) {
                $status = false;
            }
        }
    }
    
    return $status;
}

function determineSquare($y, $x) {
    if ($y >= 0 && $y <= 2 && $x >= 0 && $x <= 2) {
        return ["fromY" => 0, "toY" => 2, "fromX" => 0, "toX" => 2, "square" => "square1", ];
    } elseif ($y >= 0 && $y <= 2 && $x >= 3 && $x <= 5) {
        return ["fromY" => 0, "toY" => 2, "fromX" => 3, "toX" => 5, "square" => "square2", ];
    } elseif ($y >= 0 && $y <= 2 && $x >= 6 && $x <= 8) {
        return ["fromY" => 0, "toY" => 2, "fromX" => 6, "toX" => 8, "square" => "square3", ];
    } elseif ($y >= 3 && $y <= 5 && $x >= 0 && $x <= 2) {
        return ["fromY" => 3, "toY" => 5, "fromX" => 0, "toX" => 2, "square" => "square4", ];
    } elseif ($y >= 3 && $y <= 5 && $x >= 3 && $x <= 5) {
        return ["fromY" => 3, "toY" => 5, "fromX" => 3, "toX" => 5, "square" => "square5", ];
    } elseif ($y >= 3 && $y <= 5 && $x >= 6 && $x <= 8) {
        return ["fromY" => 3, "toY" => 5, "fromX" => 6, "toX" => 8, "square" => "square6", ];
    } elseif ($y >= 6 && $y <= 8 && $x >= 0 && $x <= 2) {
        return ["fromY" => 6, "toY" => 8, "fromX" => 0, "toX" => 2, "square" => "square7", ];
    } elseif ($y >= 6 && $y <= 8 && $x >= 3 && $x <= 5) {
        return ["fromY" => 6, "toY" => 8, "fromX" => 3, "toX" => 5, "square" => "square8", ];
    } elseif ($y >= 6 && $y <= 8 && $x >= 6 && $x <= 8) {
        return ["fromY" => 6, "toY" => 8, "fromX" => 6, "toX" => 8, "square" => "square9", ];
    }
}

function findEmptyCell($matrix) {
    for ($row = 0;$row < 9;$row++) {
        for ($col = 0;$col < 9;$col++) {
            if ($matrix[$row][$col] == "&nbsp;") {
                return [$row, $col];
            }
        }
    }
    
    return false;
}
function checkPossibleValidNumbers($matrix, $column, $row) {
    $numbers = [];
    for ($i = 1;$i <= 9;$i++) {
        if (checkValidity($matrix, $column, $row, $i)) {
            $numbers[] = $i;
        }
    }
    return $numbers;
}

if (isset($_REQUEST["action"])) {
    switch ($_REQUEST["action"]) {
        case "new_scheme":
            $difficulty = isset($_POST["difficulty"]) ? $_POST["difficulty"] : "medium";
            $matrix = createInitialScheme($difficulty);
            $originalMatrix = $matrix;
        break;
        case "solve":
            $matrix = unserialize($_POST["matrix"]);
            $originalMatrix = $matrix;
            solveSudoku($matrix);
        break;
        case "check":
            $matrix = unserialize($_POST["matrix"]);
            $row = $_POST["row"];
            $col = $_POST["col"];
            $value = $_POST["value"];
            
            if ($value !== "") {
                $correct = checkValidity($matrix, $row, $col, $value);
                solveSudoku($matrix);
                
                if ($matrix[$row][$col] == $value) {
                    echo json_encode(["correct" => true]);
                    exit();
                }
                echo json_encode(["correct" => false]);
            }
            exit();
    }
} else {
    $matrix = createInitialScheme();
    $originalMatrix = $matrix;
}

$difficulty = isset($_POST["difficulty"]) ? $_POST["difficulty"] : "medium";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thunderstorm Sudoku Puzzle</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #1a1a2e;
            overflow: hidden;
        }
        .thunderstorm-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.3);
        }
        .cloud {
            position: absolute;
            width: 300px;
            height: 100px;
            background-color: #2c3e50;
            border-radius: 50px;
            top: 20%;
            opacity: 0.9;
            animation: moveCloud 20s linear infinite;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .cloud::before,
        .cloud::after {
            content: '';
            position: absolute;
            background-color: #2c3e50;
            border-radius: 50%;
        }
        .cloud::before {
            width: 180px;
            height: 180px;
            top: -90px;
            left: 50px;
        }
        .cloud::after {
            width: 220px;
            height: 220px;
            top: -110px;
            right: 50px;
        }
        .lightning {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 3px;
            height: 0;
            background-color: #f1c40f;
            opacity: 0;
            filter: blur(1px);
        }
        @keyframes flash {
            0% { opacity: 0; height: 0; }
            10% { opacity: 1; height: 200px; }
            20% { opacity: 0; height: 200px; }
            30% { opacity: 1; height: 200px; }
            40%, 100% { opacity: 0; height: 0; }
        }
        @keyframes moveCloud {
            0% { left: -300px; }
            100% { left: 100%; }
        }
        .raindrop {
            position: absolute;
            width: 2px;
            height: 30px;
            background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, #6ab7ff 100%);
            opacity: 0.6;
            animation: fall 0.7s linear infinite;
        }
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(20deg);
            }
        }
        .thunder-sound {
            display: none;
        }
        .sudoku-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        table {
            border-collapse: collapse;
            border: 3px solid #16213e;
        }
        td {
            width: 50px;
            height: 50px;
            text-align: center;
            border: 1px solid #7f8c8d;
        }
        td:nth-child(3n) {
            border-right: 3px solid #16213e;
        }
        tr:nth-child(3n) td {
            border-bottom: 3px solid #16213e;
        }
        input {
            width: 100%;
            height: 100%;
            border: none;
            text-align: center;
            font-size: 24px;
            padding: 0;
            box-sizing: border-box;
            color: #16213e;
            background-color: transparent;
        }
        input:focus {
            outline: none;
            background-color: #e0e0e0;
        }
        input[readonly] {
            font-weight: bold;
            color: #0f3460;
        }
        input.incorrect {
            background-color: #ffcccc;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .actions input[type="button"], .actions select {
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            background-color: #0f3460;
            color: white;
            border: none;
            border-radius: 4px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .actions select {
            background-color: #16213e;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="white" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position-x: 95%;
            background-position-y: 50%;
            padding-right: 25px;
        }
        .actions input[type="button"]:hover, .actions select:hover {
            background-color: #16213e;
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }
        .actions input[type="button"]:active, .actions select:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script type="text/javascript">
      function submitForm(action) {
        var form = document.getElementById('actions');
        form.action = action;
        form.submit();
      }

      function checkInput(input, row, col) {
        var value = input.value;
        if (value === '') {
          input.classList.remove('incorrect');
          return;
        }
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?=$_SERVER["PHP_SELF"] ?>?action=check', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
          if (this.status === 200) {
            var response = JSON.parse(this.responseText);
			console.log(response);
            if (response.correct) {
              input.classList.remove('incorrect');
            } else {
              input.classList.add('incorrect');
            }
          }
        };
        xhr.send('row=' + row + '&col=' + col + '&value=' + value + '&matrix=' + encodeURIComponent(document.querySelector('input[name="matrix"]').value));
      }

      // Add this function to initialize the game state
      function initializeGame() {
        var inputs = document.querySelectorAll('table input');
        inputs.forEach(function(input) {
          input.addEventListener('input', function() {
            if (this.value === '') {
              this.classList.remove('incorrect');
            }
          });
        });
      }

      // Call initializeGame when the window loads
      window.addEventListener('load', initializeGame);

      function createThunderstorm() {
        const thunderstormContainer = document.createElement('div');
        thunderstormContainer.className = 'thunderstorm-container';
        document.body.appendChild(thunderstormContainer);

        function createCloud() {
          const cloud = document.createElement('div');
          cloud.className = 'cloud';
          cloud.style.top = `${Math.random() * 40}%`;
          thunderstormContainer.appendChild(cloud);

          const lightning = document.createElement('div');
          lightning.className = 'lightning';
          cloud.appendChild(lightning);

          setTimeout(() => {
            cloud.remove();
          }, 20000);
        }

        function flash(lightning) {
          lightning.style.animation = 'flash 1s';	
          setTimeout(() => {
            lightning.style.animation = '';
          }, 500);
        }

        function createRaindrop() {
          const raindrop = document.createElement('div');
          raindrop.className = 'raindrop';
          raindrop.style.left = `${Math.random() * 100}%`;
          raindrop.style.animationDuration = `${0.3 + Math.random() * 0.5}s`;
          raindrop.style.opacity = Math.random() * 0.4 + 0.2;
          thunderstormContainer.appendChild(raindrop);

          setTimeout(() => {
            raindrop.remove();
          }, 1000);
        }

        setInterval(createCloud, 5000);
        setInterval(() => {
          const lightnings = document.querySelectorAll('.lightning');
          if (lightnings.length > 0) {
            flash(lightnings[Math.floor(Math.random() * lightnings.length)]);
          }
        }, 2000 + Math.random() * 3000);
        setInterval(createRaindrop, 20);
      }

      window.onload = function() {
        createThunderstorm();
      };
    </script>
</head>
<body>
    <div class="sudoku-container">
        <table>
            <?php for ($row = 0;$row < 9;$row++) { ?>
                <tr>
                    <?php for ($col = 0;$col < 9;$col++) { ?>
                        <td>
                            <?php if ($originalMatrix[$row][$col] == $matrix[$row][$col] && $matrix[$row][$col] != "&nbsp;") { ?>
                                <input type="text" value="<?php echo $matrix[$row][$col]; ?>" readonly style="font-weight: bold; color: #000;">
                            <?php
        			} else { ?>
                                <input type="text" value="<?php echo $matrix[$row][$col] != "&nbsp;" ? $matrix[$row][$col] : ""; ?>" maxlength="1" oninput="checkInput(this, <?php echo $row; ?>, <?php echo $col; ?>)">
                            <?php
        			} ?>
                        </td>
                    <?php
    			} ?>
                </tr>
            <?php
		} ?>
        </table>

        <form id="actions" method="POST">
            <input type='hidden' name='matrix' value="<?php echo htmlentities(serialize($matrix)); ?>" />
            <div class="actions">
                <select name="difficulty">
                    <option value="easy" <?php echo $difficulty == "easy" ? "selected" : ""; ?>>Easy</option>
                    <option value="medium" <?php echo $difficulty == "medium" ? "selected" : ""; ?>>Medium</option>
                    <option value="hard" <?php echo $difficulty == "hard" ? "selected" : ""; ?>>Hard</option>
                    <option value="expert" <?php echo $difficulty == "expert" ? "selected" : ""; ?>>Expert</option>
                </select>
                <input type="button" onclick="submitForm('<?=$_SERVER["PHP_SELF"] ?>?action=new_scheme')" value="New Puzzle" />
                <input type="button" onclick="submitForm('<?=$_SERVER["PHP_SELF"] ?>?action=solve')" value="Solve" />
            </div>
        </form>
    </div>
</body>
</html>
