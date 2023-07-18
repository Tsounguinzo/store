let error_message = document.getElementById("error_message");

function validateForm(){
    let password = document.getElementById("password").value;
    let phone = document.getElementById("tel").value.replace(/\s/g,"");
    let PasswordRegex  = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+-=[]{}|;':",.<>?]).{8,}$/;
    let phoneRegex  = /^\+243\d{8}$/;

    if(!phoneRegex.test(phone)){
        error_message.value = "Le numéro doit commencer par '+243' suivi de 8 chiffres"
        return false;
    }

    if(!PasswordRegex.test(password)){
        error_message.value = "Le mot de passe doit comporter au moins 8 caractères et inclure des lettres majuscules, des lettres minuscules, des chiffres et des caractères spéciaux."
        return false;
    }

    error_message.value = ""
    return true;


}