<?php
include 'connec.php';

$sql = "SELECT 
                    e.id,
                    e.academic_year,
                    e.student_id,
                    e.speciality_id,
                    e.level_id,
                    CONCAT(s.firstName, ' ', s.lastName) AS student_name,
                    sp.name AS specialty_name,
                    l.name AS level_name
            FROM enrollment e
            INNER JOIN student s ON e.student_id = s.id
            INNER JOIN speciality sp ON e.speciality_id = sp.id
            INNER JOIN level l ON l.id = e.level_id
            ORDER BY e.id ASC;";

$stmt = $conn->query($sql);
$enrollments = $stmt->fetchAll();

//the total of enrollments
$totalenrollments = count($enrollments);

// $students = [];

$query = "SELECT e.id, 
                    e.academic_year,
                    e.student_id,
                    e.speciality_id,
                    e.level_id,
                    CONCAT(s.firstName, ' ', s.lastName) AS student_name,
                    sp.name AS speciality_name, 
                    l.name AS level_name 
                    FROM enrollment e
                    LEFT JOIN student s ON e.student_id = s.id
                    LEFT JOIN speciality sp ON e.speciality_id = sp.id
                    LEFT JOIN level l ON e.level_id = l.id
                    WHERE 1=1";

$params = [];

$stmt = $conn->query($query);
$enos = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $dept_id = $_POST['department'] ?? '';
    $level_id = $_POST['level'] ?? '';



    if (!empty($dept_id)) {
        $query .= " AND sp.departement_id = :dept_id";
        $params[':dept_id'] = $dept_id;
    }

    if (!empty($level_id)) {
        $query .= " AND e.level_id = :level_id";
        $params[':level_id'] = $level_id;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $enos = $stmt->fetchAll();
}

//add department function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_enrollment'])) {
    $student = $_POST['student_id'];
    $specialty = $_POST['speciality_id'] ?? '';
    $level = $_POST['level_id'] ?? '';



    if (!empty($student)  && !empty($specialty) && !empty($level)) {
        try {
            $stmt = $conn->prepare("INSERT INTO enrollment (student_id, speciality_id, level_id) VALUES (?, ?, ?)");
            $stmt->execute([$student, $specialty, $level]);
        } catch (PDOException $e) {
            die("Error adding enrolment: " . $e->getMessage());
        }


        header("Location: enroll.php?status=success");
        exit();
    }
    header("Location: enroll.php");
    exit();
}

//edit specialty function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_enrollment'])) {
    $id = $_POST['id'];
    $student = $_POST['student_id'];
    $speciality = $_POST['speciality_id'] ?? '';
    $level = $_POST['level_id'] ?? '';

    if (!empty($id)) {
        try {
            $stmt = $conn->prepare("UPDATE enrollment SET student_id = ?, speciality_id = ?, level_id = ? WHERE id = ?");
            $stmt->execute([$student, $speciality, $level, $id]);
        } catch (PDOException $e) {
            die("Error updating enrolment: " . $e->getMessage());
        }

        header("Location: enroll.php?status=success");
        exit();
    }
    header("Location: enroll.php");
    exit();
}

// delete function
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM enrollment WHERE id = ?");
    $stmt->execute([$id_to_delete]);

    header("Location: enroll.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pdf'])) {




    require('fpdf/fpdf.php');


    class PDF extends FPDF
    {
        // En-tête du document
        function Header()
        {
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, mb_convert_encoding('Enrollment List', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
            $this->Ln(5);
        }

        // Pied de page
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }

        // Tableau simple basé sur ton code
        function BasicTable($header, $data)
        {
            // En-tête du tableau
            $this->SetFont('Arial', 'B', 11);
            $w = array(40, 50, 60, 35); // Largeurs des colonnes

            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, mb_convert_encoding($header[$i], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            }
            $this->Ln();

            // Données du tableau
            $this->SetFont('Arial', '', 10);
            foreach ($data as $row) {
                $this->Cell($w[0], 6, mb_convert_encoding($row['academic_year'], 'ISO-8859-1', 'UTF-8'), 1);
                $this->Cell($w[1], 6, mb_convert_encoding($row['student_name'], 'ISO-8859-1', 'UTF-8'), 1);
                $this->Cell($w[2], 6, mb_convert_encoding($row['speciality_name'], 'ISO-8859-1', 'UTF-8'), 1);
                $this->Cell($w[3], 6, mb_convert_encoding($row['level_name'], 'ISO-8859-1', 'UTF-8'), 1);
                $this->Ln();
            }
        }
    }

    $dept_id = $_POST['department'] ?? '';
    $level_id = $_POST['level'] ?? '';



    if (!empty($dept_id)) {
        $query .= " AND sp.departement_id = :dept_id";
        $params[':dept_id'] = $dept_id;
    }

    if (!empty($level_id)) {
        $query .= " AND e.level_id = :level_id";
        $params[':level_id'] = $level_id;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $enose = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 1. Récupération des données depuis la base de données
    $query = "SELECT 
                        e.id,
                        e.academic_year,
                        e.student_id,
                        e.speciality_id,
                        e.level_id,
                        CONCAT(s.firstName, ' ', s.lastName) AS student_name,
                        sp.name AS specialty_name,
                        l.name AS level_name
                FROM enrollment e
                INNER JOIN student s ON e.student_id = s.id
                INNER JOIN speciality sp ON e.speciality_id = sp.id
                INNER JOIN level l ON l.id = e.level_id
                ORDER BY e.id ASC;";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Instanciation de la classe PDF
    $pdf = new PDF();
    $pdf->AliasNbPages();

    // Titres des colonnes correspondant à ton tableau
    $header = array('Academic Year', 'Student Name', 'Specialty', 'Level');

    $pdf->SetFont('Arial', '', 12);
    $pdf->AddPage();
    $pdf->BasicTable($header, $enos);
    $pdf->Output();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>total enrollment</title>

    <link rel="stylesheet" href="boostrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/3.0.3/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="select2/css/select2.min.css">
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <style>
        .content {
            width: 100%;
            min-height: 100vh;
            transition: all 2s ease;
        }

        .side-bar {
            width: 200px;
            background-color: #f8f9fa;
            padding: 20px;
            height: 100vh;
        }

        .nav-link {
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
            border-left: 4px solid transparent;
        }

        .nav-link:hover {
            color: #007bff;
            border-left-color: #007bff;
        }

        .nav-link.active {
            color: #007bff;
            border-left-color: #007bff;
        }

        form {
            padding: 20px;
            text-align: left;
        }

        .d {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        .table {

            margin-top: 20px;

        }
    </style>
    <div style="display: flex; align-items: stretch; width: 100%;">
        <nav id="sidebar">
            <div >
                <div class="row text-center" style="background-color: #007bff; padding: 10px;max-width: 213px;">
                    <div class="col-12">
                        <img src="media/LOGO-KFJ237.png" class="rounded-circle" alt="logo" width="50" height="50">
                    </div>
                    <div class="col-12">
                        <p>ADMIN</p>
                    </div>
                </div>
                <div class="side-bar">
                    <div class="nav flex-column mt-3">
                        <a href="enroll.php" class="nav-link" dep="enr">Enrollment</a>
                        <a href="departement.php" class="nav-link" id="dep">department</a>
                        <a href="speciality.php" class="nav-link" id="spec">specialty</a>
                        <a href="level.php" class="nav-link" id="lev">level</a>
                        <a href="student.php" class="nav-link" id="stu">student</a>
                        <a href="index.php" class="nav-link mt-5 " style="color: red;"><i
                                class="fas fa-sign-out-alt mr-3"></i>Logout</a>
                    </div>
                </div>
            </div>

        </nav>
        <div class="content">

            <nav class="navbar navbar-expand-lg navbar-custom" style="background-color: #007bff;">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn text-white fs-4">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="ms-auto text-white">
                        <i class="fa-solid fa-bell fs-5 cursor-pointer"></i>
                    </div>
                </div>
            </nav>
            <div class="container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom" style="padding: 5px;">
                    <div>
                        <h2 class="mb-0">Enrollment Management</h2>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">
                        <i class="fas fa-plus mr-2"></i> Enroll a Student
                    </button>
                </div>

                <!-- add enrollment modal -->
                <div class="modal fade" id="addEnrollmentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post" action="" id="addEnrollmentForm">

                                <input type="hidden" name="id">



                                <div class="d">
                                    <label for="student_id">Student name</label>
                                    <select name="student_id" id="student" class="form-control">
                                        <option value="">Select student</option>
                                        <?php
                                        $sql = "SELECT id, CONCAT(firstName, ' ', lastName) AS name  FROM student";
                                        $stmts = $conn->query($sql);
                                        $students = $stmts->fetchAll();
                                        foreach ($students as $student) {
                                            echo '<option value="' . $student['id'] . '">' . $student['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="d">
                                    <label for="specialty_id">Specialty</label>
                                    <select name="speciality_id" id="speciality" class="form-control">
                                        <option value="">Select specialty</option>
                                        <?php
                                        $sql = "SELECT id, name  FROM speciality";
                                        $stmts = $conn->query($sql);
                                        $specialties = $stmts->fetchAll();
                                        foreach ($specialties as $specialty) {
                                            echo '<option value="' . $specialty['id'] . '">' . $specialty['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="d">
                                    <label for="level_id">Level</label>
                                    <select name="level_id" id="level" class="form-control">
                                        <option value="">Select level</option>
                                        <?php
                                        $sql = "SELECT id, name  FROM level";
                                        $stmts = $conn->query($sql);
                                        $levels = $stmts->fetchAll();
                                        foreach ($levels as $level) {
                                            echo '<option value="' . $level['id'] . '">' . $level['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="d">
                                    <button type="submit" class="btn btn-primary" name="add_enrollment">Enrollment Student</button>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- edit enrollment modal -->
                <div class="modal fade" id="editEnrollmentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post" action="" id="editEnrollmentForm">

                                <input type="hidden" name="id" id="idi">



                                <div class="d">
                                    <label for="student_id">Student name</label>
                                    <select name="student_id" id="student_id" class="form-control">
                                        <option value="">Select student</option>
                                        <?php
                                        $sql = "SELECT id, CONCAT(firstName, ' ', lastName) AS name  FROM student";
                                        $stmts = $conn->query($sql);
                                        $students = $stmts->fetchAll();
                                        foreach ($students as $student) {
                                            echo '<option value="' . $student['id'] . '">' . $student['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="d">
                                    <label for="specialty_id">Specialty</label>
                                    <select name="speciality_id" id="speciality_id" class="form-control">
                                        <option value="">Select specialty</option>
                                        <?php
                                        $sql = "SELECT id, name  FROM speciality";
                                        $stmts = $conn->query($sql);
                                        $specialties = $stmts->fetchAll();
                                        foreach ($specialties as $specialty) {
                                            echo '<option value="' . $specialty['id'] . '">' . $specialty['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="d">
                                    <label for="level_id">Level</label>
                                    <select name="level_id" id="level_id" class="form-control">
                                        <option value="">Select level</option>
                                        <?php
                                        $sql = "SELECT id, name  FROM level";
                                        $stmts = $conn->query($sql);
                                        $levels = $stmts->fetchAll();
                                        foreach ($levels as $level) {
                                            echo '<option value="' . $level['id'] . '">' . $level['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="d">
                                    <button type="submit" class="btn btn-primary" name="edit_enrollment">save changes</button>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="container-fluid main-content mt-4">

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="card border-0 shadow-sm p-3">
                                <span class="text-muted">Total Enrollments</span>
                                <h3 class="fw-bold text-dark mb-0"><?php echo $totalenrollments  ?></h3>
                            </div>
                        </div>
                        <div class="col-6"></div>
                    </div>
                    <form action="" method="post">
                        <div class="d">
                            <button class="btn btn-primary" name="pdf">
                                <i class="fas fa-pdf mr-2"></i> load PDF
                            </button>
                        </div>
                    </form>






                    <div class="card p-3 table-responsive-sm shadow-sm ">

                        <form action="" method="post">
                            <div class="row">
                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label for="department">Department</label>
                                        <select name="department" id="department" class="form-control">
                                            <option value="">Select Department</option>
                                            <?php
                                            $sql = "SELECT id, name FROM deparment";
                                            $stmt = $conn->query($sql);
                                            $departments = $stmt->fetchAll();
                                            foreach ($departments as $department) {
                                                echo '<option value="' . $department['id'] . '">' . $department['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label for="level">level</label>
                                        <select name="level" class="form-control">
                                            <option value="">Select level</option>
                                            <?php
                                            $sql = "SELECT id, name FROM level";
                                            $stmt = $conn->query($sql);
                                            $levels = $stmt->fetchAll();
                                            foreach ($levels as $level) {
                                                echo '<option value="' . $level['id'] . '">' . $level['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label>Search Student</label>
                                        <button name="search" type="submit" class="btn btn-primary form-control">
                                            <i class="fa-solid fa-magnifying-glass me-2"></i> .............
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </form>

                        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])): ?>
                            <table class="table table-hover mb-0 table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>academic year</th>
                                        <th>student name</th>
                                        <th>specialty</th>
                                        <th>level</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enos as $enrollment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($enrollment['academic_year']); ?></td>
                                            <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($enrollment['speciality_name']); ?></td>
                                            <td><?php echo htmlspecialchars($enrollment['level_name']); ?></td>
                                            <td class="text-center">
                                                <button
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editEnrollmentModal"
                                                    title="modify"
                                                    class="btn btn-sm btn-outline-info edit-btn"
                                                    data-id="<?= $enrollment['id'] ?>"
                                                    data-student_id="<?= $enrollment['student_id'] ?>"
                                                    data-speciality_id="<?= $enrollment['speciality_id'] ?>"
                                                    data-level_id="<?= $enrollment['level_id'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a
                                                    href="?action=delete&id=<?= $enrollment['id'] ?>"
                                                    title="delete"
                                                    onclick="return confirm('do you want to delete this enrollment ? ')"
                                                    class="btn btn-sm btn-outline-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        <?php endif; ?>


                    </div>
                </div>

                <div class="row bg-dark text-light" style="margin-top: 25px;">

                    <div class="col-12">
                        <footer>
                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <h4 style="margin-top: 5px;" class="text-center">my motto</h4>
                                    <div class="row">
                                        <div class="col-12">
                                            <div style=" margin: 10px;">
                                                <a href="#"><img src="media/LOGO-KFJ237.png" class="img-fluid rounded-circle"
                                                        alt="LOGO" width="70" height="70"></a>
                                            </div>
                                            <div style=" margin: 10px;">
                                                <p>your goals, my code:</p>
                                                <p>transforming complex technical needs into simple, secure, and seamless
                                                    digital
                                                    experience
                                                </p>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <h4 style="margin-top: 5px;" class="text-center">my services</h4>
                                    <div class="row text-light">
                                        <div class="col-sm-6 col-12">
                                            <h3 class="text-light">Web development</h3>
                                            <ul>
                                                <li>professional showcase websites(vitrine)</li>
                                                <li>custom web application and portals</li>
                                                <li>landing page and sales tunnels</li>
                                                <li>portfolio and interactive resume sites</li>
                                                <li>e-commerce and online product catalogs</li>
                                                <li>membership portals and online course platforms</li>
                                                <li>dynamic forms and booking systems</li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-6 col-12">
                                            <h3 class="text-light">social network</h3>
                                            <ul>
                                                <li>tiktok monetizable account</li>
                                                <li>content creation training</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style=" color: whitesmoke;">
                            <div style="padding: 5px; text-align: center;">
                                <p>Copyright &COPY; 2026 kana junior(KFJ237) </p>
                            </div>
                        </footer>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="jquery/jquery-4.0.0.js"></script>
    <script src="boostrap5/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/3.0.3/js/dataTables.bootstrap5.min.js"></script>
    <script>
        jQuery.isArray = Array.isArray;

        if (!jQuery.trim) {
            jQuery.trim = function(text) {
                return text == null ? "" : (text + "").replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
            };
        }
    </script>
    <script src="select2/js/select2.min.js"></script>
    <script src="script.js"></script>
    <script>
        $(document).ready(function() {
            $('#enr').addClass('active');



            // Handle edit button click
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                let student_id = $(this).data('student_id');
                let speciality_id = $(this).data('speciality_id');
                let level_id = $(this).data('level_id');




                $('#idi').val(id);
                $('#student_id').val(student_id).trigger('change');
                $('#speciality_id').val(speciality_id).trigger('change');
                $('#level_id').val(level_id).trigger('change');

            });


        });
    </script>
</body>

</html>