<?php
// MySQL kapcsolat beállítások
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'school';


//whoaoaa






// MySQL kapcsolat létrehozása
$conn = new mysqli($host, $username, $password);

// Ellenőrizzük a kapcsolatot
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Funkciók az adatbázis és táblák létrehozásához
function createDatabase($conn) {
    $sql = "CREATE DATABASE IF NOT EXISTS school CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci";
    if ($conn->query($sql) === TRUE) {
        echo "Database 'school' created successfully.<br>";
    } else {
        echo "Error creating database: " . $conn->error . "<br>";
    }
}

function createStudentsTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS students (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                class_id INT
            ) ENGINE=InnoDB";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'students' created successfully.<br>";
    } else {
        echo "Error creating table 'students': " . $conn->error . "<br>";
    }
}

function createSubjectsTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS subjects (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL
            ) ENGINE=InnoDB";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'subjects' created successfully.<br>";
    } else {
        echo "Error creating table 'subjects': " . $conn->error . "<br>";
    }
}

function createClassesTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS classes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                year INT
            ) ENGINE=InnoDB";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'classes' created successfully.<br>";
    } else {
        echo "Error creating table 'classes': " . $conn->error . "<br>";
    }
}

function createMarksTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS marks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT,
                subject_id INT,
                mark INT,
                date DATE,
                FOREIGN KEY (student_id) REFERENCES students(id),
                FOREIGN KEY (subject_id) REFERENCES subjects(id)
            ) ENGINE=InnoDB";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'marks' created successfully.<br>";
    } else {
        echo "Error creating table 'marks': " . $conn->error . "<br>";
    }
}

function generateRandomData($conn) {
    // Véletlenszerű tantárgyak
    $subjects = ['Matematika', 'Fizika', 'Kémia', 'Irodalom', 'Történelem'];
    foreach ($subjects as $subject) {
        $sql = "INSERT INTO subjects (name) VALUES ('$subject')";
        $conn->query($sql);
    }

    // Véletlenszerű osztályok
    $classes = ['1.A', '1.B', '2.A', '2.B', '3.A', '3.B'];
    foreach ($classes as $class) {
        $year = rand(2020, 2025);
        $sql = "INSERT INTO classes (name, year) VALUES ('$class', $year)";
        $conn->query($sql);
    }

    // Véletlenszerű tanulók
    $students = ['Anna', 'Béla', 'Cecília', 'Dániel', 'Emília', 'Feri', 'Gábor', 'Hanna', 'István', 'Júlia', 'Katalin', 'László', 'Mária', 'Nóra', 'Péter'];
    $class_ids = [1, 2, 3, 4, 5, 6];
    foreach ($students as $student) {
        $class_id = $class_ids[array_rand($class_ids)];
        $sql = "INSERT INTO students (name, class_id) VALUES ('$student', $class_id)";
        $conn->query($sql);
    }

    // Véletlenszerű jegyek
    $student_result = $conn->query("SELECT id FROM students");
    $subject_result = $conn->query("SELECT id FROM subjects");
    while ($student_row = $student_result->fetch_assoc()) {
        $student_id = $student_row['id'];
        $subject_result->data_seek(0);
        while ($subject_row = $subject_result->fetch_assoc()) {
            $subject_id = $subject_row['id'];
            $marks = rand(1, 5);
            $date = date('Y-m-d', rand(strtotime('2023-01-01'), strtotime('2023-12-31')));
            $sql = "INSERT INTO marks (student_id, subject_id, mark, date) VALUES ($student_id, $subject_id, $marks, '$date')";
            $conn->query($sql);
        }
    }
}

// Gomb megjelenítése
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createDatabase($conn);
    $conn->select_db($dbname);
    createStudentsTable($conn);
    createSubjectsTable($conn);
    createClassesTable($conn);
    createMarksTable($conn);
    generateRandomData($conn);
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adatbázis Létrehozása</title>
</head>
<body>
    <h1>Adatbázis létrehozása</h1>
    <form method="post">
        <button type="submit">Adatbázis létrehozása</button>
    </form>
</body>
</html>

<?php
// Kapcsolat lezárása
$conn->close();
?>
