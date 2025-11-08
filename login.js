document.getElementById('toLogin').addEventListener('click', () => {
    document.getElementById('login').classList.add('active');
    document.getElementById('signup').classList.remove('active');
});

document.getElementById('toSignUp').addEventListener('click', () => {
    document.getElementById('signup').classList.add('active');
    document.getElementById('login').classList.remove('active');
});
