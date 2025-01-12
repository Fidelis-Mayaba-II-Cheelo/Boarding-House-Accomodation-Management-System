<?php
include_once(__DIR__ . '/../../../../includes/autoloader.php');

class StudentRegistrationModel extends PDOSingleton {
    protected $student_name;
    protected $student_number;
    protected $email;
    protected $national_registration;
    protected $profile_picture;
    protected $gender;
    protected $date_of_birth;
    protected $program_of_study;
    protected $year_of_study;
    protected $phone_number;
    protected $guardian_phone_number;
    protected $password;

    public function __construct($student_name, $student_number, $email, $national_registration, $profile_picture, $gender, $date_of_birth, $program_of_study, $year_of_study, $phone_number, $guardian_phone_number, $password){
        $this->student_name = $student_name;
        $this->student_number = $student_number;
        $this->email = $email;
        $this->national_registration = $national_registration;
        $this->profile_picture = $profile_picture;
        $this->gender = $gender;
        $this->date_of_birth = $date_of_birth;
        $this->program_of_study = $program_of_study;
        $this->year_of_study = $year_of_study;
        $this->phone_number = $phone_number;
        $this->guardian_phone_number = $guardian_phone_number;
        $this->password = $password;  
    }

    public function register(){
        $sql = "INSERT INTO `students` (`student_name`, `student_number`, `email`, `national_registration`, `profile_picture`, `gender`, `date_of_birth` ,`program_of_study`, `year_of_study`, `phone_number`, `guardian_phone_number`, `password`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $db = PDOSingleton::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$this->student_name, $this->student_number, $this->email, $this->national_registration, $this->profile_picture, $this->gender, $this->date_of_birth, $this->program_of_study, $this->year_of_study, $this->phone_number, $this->guardian_phone_number, $this->password]);
        return $result;
    }
}