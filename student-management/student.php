<?php
include 'connec.php';


$stmt = $conn->query("SELECT * FROM student");
$students = $stmt->fetchAll();
//the total of students
$totalStudents = count($students);

//add student function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $date = $_POST['date'] ?? '';

    $media = 'media/' . time() . '_' . $_FILES['photo']['name'];

    move_uploaded_file($_FILES['photo']['tmp_name'], $media);


    if (!empty($fname)  && !empty($lname)) {
        try {
            $stmt = $conn->prepare("INSERT INTO student (firstName, lastName, birth, media) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fname, $lname, $date, $media]);
        } catch (PDOException $e) {
            die("Error adding student: " . $e->getMessage());
        }


        header("Location: student.php?status=success");
        exit();
    }
    header("Location: student.php");
    exit();
}

//edit student function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_student'])) {
    $id = $_POST['id'] ?? '';
    $fname = $_POST['fname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $date = $_POST['date'] ?? '';

    $media = $_POST['old'];

    if (isset($_FILES['photo']['error']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

        $nmedia = 'media/' . time() . '_' . $_FILES['photo']['name'];
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $nmedia)) {
            $media = $nmedia;
        }
    }




    $stmt = $conn->prepare("UPDATE student SET firstName = ?, lastName = ?, birth = ?, media = ? WHERE id = ?");
    $stmt->execute([$fname, $lname, $date, $media, $id]);



    header("Location: student.php?status=success");
    exit();

    header("Location: student.php");
    exit();
}

// delete function
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id_to_delete = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM student WHERE id = ?");
    $stmt->execute([$id_to_delete]);

    header("Location: student.php");
    exit();
}

// spl_autoload_register(function($class){
//     $faker_prefix = 'Faker\\';
//     $faker_dir = __DIR__ . '/vendor/Faker-1.23.1/src/Faker/';

//     $len = strlen($faker_prefix);
//     if(strncmp($faker_prefix, $class, $len) !== 0) {
//         return;
//     }

//     $relative_class = substr($class, $len);
//     $file = $faker_dir . str_replace('\\', '/', $relative_class) . '.php';

//     if (file_exists($file)) {
//         require $file;
//     }

//     $psr_prefix = 'Psr\\Container\\';
//     $psr_dir = __DIR__ . 'vendor/Psr/Container/';
//     if (strncmp($psr_prefix, $class, strlen($psr_prefix)) === 0) {
//         $file = $psr_dir . str_replace('\\', '/', substr($class, strlen($psr_prefix))) . '.php';
//         if (file_exists($file)) { require_once $file; return; }
//     }
// });
// // require_once __DIR__ . '/vendor/faker-2.0/src/Factory.php';
// $faker = Faker\Factory::create();

// if (isset($_GET['generate_faker'])) { 
//     $sql = "INSERT INTO student (firstName, lastName, birth, media) VALUES (:firstName, :lastName, :birth, :media)"; 
//     $stmt = $conn->prepare($sql);

//     for ($i = 0; $i < 50; $i++) {
//         $stmt->execute([
//             ':firstName' => $faker->firstName(),
//             ':lastName'  => $faker->lastName(),
//             ':birth'     => $faker->date('Y-m-d', '2005-12-31'),
//             ':media'     => 'https://i.pravatar.cc/150?img=' . rand(1, 70) // Génère une fausse photo aléatoire
//         ]);
//     }

//     // Redirige vers la page pour afficher le résultat
//     header("Location: student.php");
//     exit();
// }



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>student management</title>

    <link rel="stylesheet" href="boostrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/3.0.3/css/dataTables.bootstrap5.min.css">
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
            <div class="ss">
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

        <div class="content ">

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
                        <h2 class="mb-0">Student Management</h2>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fas fa-plus mr-2"></i> Student
                    </button>
                </div>
                <!-- add student modal -->
                <div class="modal fade" id="addStudentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post" id="addStudentForm" enctype="multipart/form-data">

                                <div class="d">
                                    <label for="fname">first Name</label>
                                    <input type="text" name="fname" class="form-control" required placeholder="ex.Tom">
                                </div>

                                <div class="d">
                                    <label for="lname">last Name</label>
                                    <input type="text" name="lname" class="form-control" required placeholder="ex.John">
                                </div>

                                <div class="d">
                                    <label for="date">Date of birth</label>
                                    <input type="date" name="date" class="form-control" required>
                                </div>

                                <div class="d">
                                    <label for="photo">Photo</label>
                                    <input type="file" name="photo" accept="image/*" class="form-control" required>
                                </div>

                                <div class="d">
                                    <button type="submit" class="btn btn-primary" name="add_student">Add Student</button>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <!-- edit student modal -->
                <div class="modal fade" id="editStudentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post" id="editStudentForm" enctype="multipart/form-data">

                                <input type="hidden" name="id" id="id">

                                <div class="d">
                                    <label for="fname">first Name</label>
                                    <input type="text" name="fname" id="f" class="form-control" required>
                                </div>

                                <div class="d">
                                    <label for="lname">last Name</label>
                                    <input type="text" name="lname" id="l" class="form-control" required>
                                </div>

                                <div class="d">
                                    <label for="date">Date of birth</label>
                                    <input type="date" name="date" id="d" class="form-control" required>
                                </div>

                                <input type="hidden" name="old" id="old">
                                <div class="d">
                                    <label for="photo">Photo</label>
                                    <img src="" alt="photo" id="prev" width="70" height="70" style="object-fit: cover; border-radius: 1px;">
                                    <input type="file" name="photo" id="p" accept="image/*" class="form-control" style="margin-top: 10px;" required>
                                </div>

                                <div class="d">
                                    <button type="submit" class="btn btn-primary" name="edit_student">save changes</button>
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
                                <span class="text-muted">Total Students</span>
                                <h3 class="fw-bold text-dark mb-0"><?php echo $totalStudents; ?></h3>
                            </div>
                        </div>
                        <div class="col-6">

                        </div>
                    </div>

                    <div class="card-body p-0 table-responsive-sm ">
                        <table class="table table-hover mb-0 table">
                            <thead class="thead-light">
                                <tr>
                                    <th>First name</th>
                                    <th>Last name</th>
                                    <th>Date</th>
                                    <th>photo</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['firstName']); ?></td>
                                        <td><?php echo htmlspecialchars($student['lastName']); ?></td>
                                        <td><?php echo htmlspecialchars($student['birth']); ?></td>
                                        <td> <img src="<?php echo htmlspecialchars($student['media']); ?>" alt="photo" width="50" height="50" style="object-fit: cover; border-radius: 50%;"></td>
                                        <td class="text-center">
                                            <button
                                                data-bs-toggle="modal"
                                                data-bs-target="#editStudentModal"
                                                title="modify"

                                                data-id="<?= $student['id']; ?>"
                                                data-fname="<?= $student['firstName']; ?>"
                                                data-lname="<?= $student['lastName']; ?>"
                                                data-date="<?= $student['birth']; ?>"
                                                data-photo="<?= $student['media']; ?>"
                                                class="btn btn-sm btn-outline-info edit-btn">


                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a
                                                href="?action=delete&id=<?= $student['id'] ?>"
                                                title="delete"
                                                onclick="return confirm('do you want to delete this student ? ')"
                                                class="btn btn-sm btn-outline-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
    <script src="script.js"></script>
    <script>
        $(document).ready(function() {
            $('#stu').addClass('active');

            // Handle edit button click
            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var fname = $(this).data('fname');
                var lname = $(this).data('lname');
                var date = $(this).data('date');
                var photo = $(this).data('photo');

                $('#id').val(id);
                $('#f').val(fname);
                $('#l').val(lname);
                $('#d').val(date);

                $('#old').val(photo);

                if (photo) {
                    $('#prev').attr('src', photo).show();
                } else {
                    $('#prev').hide();
                }
            });


        });
    </script>
</body>

</html>