export const validateEmail = (email) => {
    return /^.+@.+\..+$/i.test(email);
}

export const validateName = (name) => {
    return /^[a-z\d_]{2,25}$/i.test(name);
}

export const validatePassword = (password) => {
    return /^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/.test(password);
}
