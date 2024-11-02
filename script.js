function toggleAnswerFields() {
    const questionType = document.getElementById("question_type").value;
    const multipleChoiceFields = document.getElementById("multiple_choice_fields");
    const trueFalseFields = document.getElementById("true_false_fields");

    if (questionType === "multiple") {
        multipleChoiceFields.style.display = "block";
        trueFalseFields.style.display = "none";
    } else if (questionType === "true_false") {
        multipleChoiceFields.style.display = "none";
        trueFalseFields.style.display = "block";
    }
}