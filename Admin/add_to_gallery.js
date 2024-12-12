document.addEventListener("DOMContentLoaded", () => {
    const hostel_type = document.getElementById("hostel_type");
    const hostel_image = document.getElementById("hostel_image");
    const hostel_image_validation = document.getElementById("hostel_image_validation");
    const hostel_type_validation = document.getElementById("hostel_type_validation");

    function setBackgroundColor(element, message){
        element.style.backgroundColor = message === 'success' ? 'green' : 'red';
    }

    hostel_type.addEventListener("change", () => {
        if(hostel_type.value === ""){
            setBackgroundColor(hostel_type_validation, 'error');
            document.getElementById("hostel_type_validation").textContent = "Please the hostel for which you would like to add an image.";
        } else {
            setBackgroundColor(hostel_type_validation, 'success');
            document.getElementById("hostel_type_validation").textContent = "Hostel selected successfully.";
        }
    })

    hostel_image.addEventListener("change", () => {
        if(hostel_image.value === ""){
            setBackgroundColor(hostel_image_validation, 'error');
            document.getElementById("hostel_image_validation").textContent = "Please upload an image related to the hostel.";
        } else {
            setBackgroundColor(hostel_image_validation, 'success');
            document.getElementById("hostel_image_validation").textContent = "hostel image uploaded successfully.";
        }
    })
})

