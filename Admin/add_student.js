document.addEventListener('DOMContentLoaded', () => {
  const student_name = document.getElementById('student_name')
  const student_number = document.getElementById('student_number')
  const program_of_study = document.getElementById('program_of_study')
  const year_of_study = document.getElementById('year_of_study')
  const phone_number = document.getElementById('phone_number')
  const guardian_phone_number = document.getElementById('guardian_phone_number')
  const email = document.getElementById('email')
  const hostel = document.getElementById('hostel')
  const room_number = document.getElementById('room_number')
  const bedspace_number = document.getElementById('bedspace_number')
  const password = document.getElementById('password')
  const profile_picture = document.getElementById('profile_picture')
  const national_registration = document.getElementById('national_registration')
  const gender = document.getElementById('gender')
  const date_of_birth = document.getElementById('date_of_birth')


  function setBackgroundColor(element, message){
            element.style.backgroundColor = message === 'success' ? 'green' : 'red';  
  }

  profile_picture.addEventListener('change', () => {
    if (profile_picture.files && profile_picture.files[0]) {
        setBackgroundColor(profile_picture_validation, 'success');
        document.getElementById('profile_picture_validation').textContent = "Image selected Successfully";
    } else {
        setBackgroundColor(profile_picture_validation, 'error');
        document.getElementById('profile_picture_validation').textContent = "No image selected";
    }
});

  student_name.addEventListener('input', () => {
    if (student_name.value !== '' && student_name.value.length > 5) {
       setBackgroundColor(student_name_validation, 'success');
      document.getElementById('student_name_validation').textContent =
        'Student name is valid'
    } else {
        setBackgroundColor(student_name_validation, 'error');
      document.getElementById('student_name_validation').textContent =
        'Please enter a student name'
    }
  })

  student_number.addEventListener('input', () => {
    if (student_number.value !== null && student_number.value.length === 9) {
        setBackgroundColor(student_number_validation, 'success');
      document.getElementById('student_number_validation').textContent =
        'Student number is valid'
    } else {
        setBackgroundColor(student_number_validation, 'error');
      document.getElementById('student_number_validation').textContent =
        'Student number should be 9 integers long'
    }
  })

  const nrc_pattern = /^\d{6}\/\d{2}\/\d{1}$/;

  national_registration.addEventListener('input', () => {
    if(national_registration.value !== null && nrc_pattern.test(national_registration.value)){
      setBackgroundColor(national_registration_validation, 'success');
      document.getElementById('national_registration_validation').textContent = "National Registration Number is valid";
    } else {
      setBackgroundColor(national_registration_validation, 'error');
      document.getElementById('national_registration_validation').textContent = "National Registration Number is invalid, if should be in the formate xxxxxx/xx/x";
    }
  })

  const genders = ["Male", "Female"];
  gender.addEventListener('change', () =>{
    if(gender.value !== null && genders.indexOf(gender.value) !== -1){
      setBackgroundColor(gender_validation, 'success');
      document.getElementById('gender_validation').textContent = "Gender entered is valid";
    } else{
      setBackgroundColor(gender_validation, 'error');
      document.getElementById('gender_validation').textContent = "Gender entered is invalid";
    }
  })

  const dateEntered = new Date();
  dateEntered.setFullYear(dateEntered.getFullYear() - 10);
  date_of_birth.addEventListener('change', () => {
    const selectedDate = new Date(date_of_birth.value);
    if(selectedDate < dateEntered) {
      setBackgroundColor(date_of_birth_validation, 'success');
      document.getElementById('date_of_birth_validation').textContent = "Date entered is valid";
    } else{
      setBackgroundColor(date_of_birth_validation, 'error');
      document.getElementById('date_of_birth_validation').textContent = "Date entered is invalid";
    }
  })

  program_of_study.addEventListener('input', () => {
    if (
      program_of_study.value === '' &&
      !program_of_study.value.includes('Bachelor of ')
    ) {
        setBackgroundColor(student_program_of_study_validation, 'error');
      document.getElementById(
        'student_program_of_study_validation'
      ).textContent =
        'Please enter a program of study, it should begin with Bachelor of...'
    } else if (
      program_of_study.value !== '' &&
      program_of_study.value.includes('Bachelor of ')
    ) {
        setBackgroundColor(student_program_of_study_validation, 'success');
      document.getElementById(
        'student_program_of_study_validation'
      ).textContent = 'Program of study is valid'
    }
  })

  year_of_study.addEventListener('input', () => {
    const validYears = [1, 2, 3, 4]
    //Making sure that the year is an integer value
    const year = parseInt(year_of_study.value)
    //checking if the year value entered in the input field is not an integer
    if (isNaN(year)) {
        setBackgroundColor(student_year_of_study_validation, 'error');
      document.getElementById('student_year_of_study_validation').textContent =
        'Please enter an integer value for the year of study'
    } else if (!validYears.includes(year)) {
        setBackgroundColor(student_year_of_study_validation, 'error');
      document.getElementById('student_year_of_study_validation').textContent =
        'Please enter a valid year of study between 1 and 4'
    } else {
        setBackgroundColor(student_year_of_study_validation, 'success');
      document.getElementById('student_year_of_study_validation').textContent =
        'Year of study is valid'
    }
  })

  phone_number.addEventListener('input', () => {
    if (isNaN(phone_number.value) || phone_number.value.length !== 10) {
        setBackgroundColor(student_phone_number_validation, 'error');
      document.getElementById('student_phone_number_validation').textContent =
        'Please enter a valid phone number, it should be 10 digits long'
    } else {
        setBackgroundColor(student_phone_number_validation, 'success');
      document.getElementById('student_phone_number_validation').textContent =
        'Phone number is valid'
    }
  })

  guardian_phone_number.addEventListener('input', () => {
    if (
      isNaN(guardian_phone_number.value) ||
      guardian_phone_number.value.length !== 10
    ) {
        setBackgroundColor(student_guardian_phone_number_validation, 'error');
      document.getElementById(
        'student_guardian_phone_number_validation'
      ).textContent =
        "Please enter a valid phone number for the student's guardian, it should be 10 digits long"
    } else {
        setBackgroundColor(student_guardian_phone_number_validation, 'success');
      document.getElementById(
        'student_guardian_phone_number_validation'
      ).textContent = 'Guardian phone number is valid'
    }
  })

  email.addEventListener('input', () => {
    if (validator.isEmail(email.value)) {
        setBackgroundColor(student_email_validation, 'success');
      document.getElementById('student_email_validation').textContent =
        'Email is valid'
    } else if (!validator.isEmail(email.value) || email.value === '') {
        setBackgroundColor(student_email_validation, 'error');
      document.getElementById('student_email_validation').textContent =
        'Email entered is invalid, please enter a valid email address'
    }
  })

  //Validate the hostel (hostel uses select not input)
  const array_of_hostels = ['Single', 'Double', 'Triple', 'Quadruple']
  hostel.addEventListener('change', () => {
    if (array_of_hostels.indexOf(hostel.value) === -1 || hostel.value === '') {
        setBackgroundColor(student_hostel_validation, 'error');
      document.getElementById('student_hostel_validation').textContent =
        'Please select a hostel'
    } else {
        setBackgroundColor(student_hostel_validation, 'success');
      document.getElementById('student_hostel_validation').textContent =
        'Hostel selected is valid'
    }
  })

  //Validate the room number and bedspace number (they both use select not input)
  room_number.addEventListener('change', () => {
    if (room_number.value === '') {
        setBackgroundColor(student_room_number_validation, 'error');
      document.getElementById('student_room_number_validation').textContent =
        'Please select a room number'
    } else {
        setBackgroundColor(student_room_number_validation, 'success');
      document.getElementById('student_room_number_validation').textContent =
        'Room number selected is valid'
    }
  })

  bedspace_number.addEventListener('change', () => {
    if (bedspace_number.value === '') {
        setBackgroundColor(student_bedspace_number_validation, 'error');
      document.getElementById(
        'student_bedspace_number_validation'
      ).textContent = 'Please select a bedspace number'
    } else {
        setBackgroundColor(student_bedspace_number_validation, 'success');
      document.getElementById(
        'student_bedspace_number_validation'
      ).textContent = 'Bedspace number selected is valid'
    }
  })

  //Validate the password (it should match the server side validation)
  /* 
^: Start of the string.
[a-zA-Z0-9\W]: Matches any alphanumeric character (a-z, A-Z, 0-9) and any non-word character (anything that is not a letter, number, or underscore).
{8,}: Ensures the password is at least 8 characters long.
$: End of the string.
     const password_pattern = /^[a-zA-Z0-9\W]{8,}$/;
*/

  //In JavaScript, you need to use the test() method of regex patterns. Not preg_match() like in php. frontend javascript is a pain
  const password_pattern =
    /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
  password.addEventListener('input', () => {
    if (password.value.length === 0) {
        setBackgroundColor(student_password_validation, 'error');
      document.getElementById('student_password_validation').textContent =
        'Please assign a password to the student. It must be 8 characters long and must contain atleast one uppercase letter, one lowercase letter, one digit and one special character'
    } else if (!password_pattern.test(password.value)) {
        setBackgroundColor(student_password_validation, 'error');
      document.getElementById('student_password_validation').textContent =
        'Password must be 8 characters long and must contain atleast one uppercase letter, one lowercase letter, one digit and one special character'
    } else if (password_pattern.test(password.value)) {
        setBackgroundColor(student_password_validation, 'success');
      document.getElementById('student_password_validation').textContent =
        'Assigned password is valid'
    }
  })

});
