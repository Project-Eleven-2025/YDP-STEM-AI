let currentSlide = 0;
let totalSlides = 7;
let errorSlide = 0;
let showform = false;

let newUserAnalytics = {
  source: "",
  career: "",
  career1: 0,
  career2: 0,
  career3: 0,
  career4: 0,
  career5: 0,
  career6: 0,
  career7: 0,
};

function analytics(hearedFrom) {
  newUserAnalytics.source = hearedFrom;
}

function careerAssessment() {
  let career = document.querySelector('input[name="career"]:checked').value;
  newUserAnalytics.career = career;
  switch (career) {
    case "doctor":
      newUserAnalytics.career1++;
      break;
    case "nurse":
      newUserAnalytics.career2++;
      break;
    case "science-teacher":
      newUserAnalytics.career3++;
      break;
    case "math-teacher":
      newUserAnalytics.career4++;
      break;
    case "software-developer":
      newUserAnalytics.career5++;
      break;
    case "data-scientist":
      newUserAnalytics.career6++;
      break;
    default:
      newUserAnalytics.career7++;
      break;
  }
}
function averageCareerScore() {
  let career1 = newUserAnalytics.career1;
  let career2 = newUserAnalytics.career2;
  let career3 = newUserAnalytics.career3;
  let career4 = newUserAnalytics.career4;
  let career5 = newUserAnalytics.career5;
  let career6 = newUserAnalytics.career6;
  let career7 = newUserAnalytics.career7;

  let max = Math.max(
    career1,
    career2,
    career3,
    career4,
    career5,
    career6,
    career7,
  );

  if (max === career1) {
    return "Doctor";
  } else if (max === career2) {
    return "Nurse";
  } else if (max === career3) {
    return "Science Teacher";
  } else if (max === career4) {
    return "Math Teacher";
  } else if (max === career5) {
    return "Software Developer";
  } else if (max === career6) {
    return "Data Scientist";
  } else {
    return "Other";
  }
}

function showAverageCareer() {
  let averageCareer = averageCareerScore();
  document.getElementById("average-career").innerText = averageCareer;
}

function switchSlide(num) {
    const slides = document.querySelectorAll('slide');
    slides.forEach((slide, index) => {
        slide.classList.remove('active');
        if (index === num) {
            slide.classList.add('active');
        }
    });

    switch (num) {
        case 1: {
            document.getElementById("currentSlide").innerHTML = "1";
            break;
        }
        case 2: {
            document.getElementById("currentSlide").innerHTML = "2";
            document.querySelector("button").setAttribute("onclick", "analytics('social-media')");
            break;
        }
        case 3: {
            document.getElementById("currentSlide").innerHTML = "3";
            careerAssessment();
            break;
        }
        case 4: {
            document.getElementById("currentSlide").innerHTML = "4";
            careerAssessment();
            break;
        }
        case 5: {
            document.getElementById("currentSlide").innerHTML = "5";
            careerAssessment();
            break;
        }
        case 6: {
            document.getElementById("currentSlide").innerHTML = "6";
            careerAssessment();
            break;
        }
        case 7: {
            document.getElementById("currentSlide").innerHTML = "7";
            showAverageCareer();
            break;
        }
        default: {
            document.getElementById("currentSlide").innerHTML = "Error";
            document.getElementById("totalSlide").innerHTML = "Occured";
            break;
        }
    }
}

function isVisibleForm(showform) {
  if (showform) {
    document.getElementById("form").style.display = "block";
  } else {
    document.getElementById("form").style.display = "none";
  }
}

function nextSlide() {
    currentSlide++;
    if (currentSlide >= 7 && averageCareerScore != 0) {
        showform = true;
        isVisibleForm(showform);
    } else {
        showform = false;
        isVisibleForm(showform);
    }
    switchSlide(currentSlide);
}

nextSlide();
