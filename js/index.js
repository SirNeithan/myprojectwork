// Toggle between forms
const signInForm = document.getElementById('signInForm');
const signUpForm = document.getElementById('signUpForm');
const showSignUpLink = document.getElementById('showSignUp');
const showSignInLink = document.getElementById('showSignIn');

document.getElementById("showSignUp").onclick = () => {
  document.getElementById("signInForm").style.display = "none";
  document.getElementById("signUpForm").style.display = "block";
};

document.getElementById("showSignIn").onclick = () => {
  document.getElementById("signInForm").style.display = "block";
  document.getElementById("signUpForm").style.display = "none";
};

// Login form validation
document.getElementById('loginForm').addEventListener('submit', function (event) {
  const email = this.email.value.trim();
  const password = this.password.value.trim();

  if (!email || !password) {
    event.preventDefault();
    alert("Please fill in all login fields.");
  }
});

// Optional: Add client-side validation for signup as well
document.getElementById('registerForm').addEventListener('submit', function (event) {
  const fields = ['username', 'firstName', 'lastName', 'phone', 'email', 'password'];
  for (let field of fields) {
    if (!this[field].value.trim()) {
      event.preventDefault();
      alert("Please fill in all registration fields.");
      return;
    }
  }
});
