<?php

    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'school_1';
    try {
        $conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    $dept_id = $_GET['department'] ?? '';
    $level_id = $_GET['level'] ?? '';

    $query = "SELECT e.academic_year, CONCAT(s.firstName, ' ', s.lastName) AS student_name, 
                    sp.name AS specialty_name, l.name AS level_name
            FROM enrollment e
            JOIN student s ON e.student_id = s.id
            JOIN speciality sp ON e.speciality_id = sp.id
            JOIN level l ON e.level_id = l.id
            WHERE 1=1";

    $params = [];
    if ($dept_id) { $query .= " AND sp.departement_id = :d"; $params[':d'] = $dept_id; }
    if ($level_id) { $query .= " AND e.level_id = :l"; $params[':l'] = $level_id; }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    $this->Cell($w[2], 6, mb_convert_encoding($row['specialty_name'], 'ISO-8859-1', 'UTF-8'), 1);
                    $this->Cell($w[3], 6, mb_convert_encoding($row['level_name'], 'ISO-8859-1', 'UTF-8'), 1);
                    $this->Ln();
                }
            }
        }
?>