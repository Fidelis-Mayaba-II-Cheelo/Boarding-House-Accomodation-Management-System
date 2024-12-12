document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".carousel").forEach((carousel) => {
    const images = carousel.querySelectorAll(".carousel-image");
    const totalImages = images.length;
    let currentStartIndex = 0;

    function updateCarousel() {
      images.forEach((img, index) => {
        img.style.display =
          index >= currentStartIndex && index < currentStartIndex + 3
            ? "inline-block"
            : "none";
      });
    }

    carousel.querySelector(".prev").addEventListener("click", () => {
      currentStartIndex = (currentStartIndex - 3 + totalImages) % totalImages;
      updateCarousel();
    });

    carousel.querySelector(".next").addEventListener("click", () => {
      currentStartIndex = (currentStartIndex + 3) % totalImages;
      updateCarousel();
    });

    updateCarousel();
  });
});

// Get the modal
var modal = document.getElementById("imageModal");

// Get the image and insert it inside the modal
var modalImg = document.getElementById("modalImage");
var captionText = document.getElementById("caption");

// Get all images in the carousel
var images = document.querySelectorAll(".carousel img");

// Loop through each image and add click event listener
images.forEach(function (image) {
  image.onclick = function () {
    modal.style.display = "block"; // Show modal
    modalImg.src = this.src; // Set image source to clicked image
    captionText.innerHTML = this.alt; // Set caption (alt text) of the image
  };
});

// Close the modal when the user clicks on the close button
function closeModal() {
  modal.style.display = "none";
}
