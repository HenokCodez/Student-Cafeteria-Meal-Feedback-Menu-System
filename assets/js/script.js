function validateRegistration() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    
    if (password.length < 6) {
        alert("Password is too short. Minimum 6 characters required.");
        return false;
    }
    
    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return false;
    }
    
    return true;
}

function confirmAction() {
    return confirm("Are you sure you want to perform this action? This cannot be undone.");
}
