import Cookies from 'js-cookie';
import { INCLUDE, CSRF_TOKEN_COOKIE, CSRF_TOKEN_HEADER, MESSAGES_URL } from './constants';

const processResponse = response => {
    return response
      .json()
      .then(json => {
        if (json.status && json.status !== 'OK' || json.errors) {
          throw {
            errors: json.errors,
            status: response.status
          };
        }
        return json;
      });
};

export const getMessages = (isAuthenticated) => {
  const headers = isAuthenticated ? {
    'Content-Type': 'application/json',
  } : {
    'Content-Type': 'application/json',
    [CSRF_TOKEN_HEADER]: Cookies.get(CSRF_TOKEN_COOKIE)
  }

  return fetch(MESSAGES_URL, {
    method: 'GET',
    headers: headers,
  }).then(processResponse);
};

export const addMessage = (message, parentId) => {
  return fetch(MESSAGES_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      [CSRF_TOKEN_HEADER]: Cookies.get(CSRF_TOKEN_COOKIE)
    },
    body: JSON.stringify({
      text: message,
      parent_id: parentId
    }),
    credentials: INCLUDE
  }).then(processResponse);
};

export const removeMessage = id => {
  const url = MESSAGES_URL + id;
  return fetch(url, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json',
      [CSRF_TOKEN_HEADER]: Cookies.get(CSRF_TOKEN_COOKIE)
    },
    credentials: INCLUDE
  }).then(processResponse);
};

export const updateMessage = (id, text) => {
  const url = MESSAGES_URL + id;
  return fetch(url, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      [CSRF_TOKEN_HEADER]: Cookies.get(CSRF_TOKEN_COOKIE)
    },
    body: JSON.stringify({ text }),
    credentials: INCLUDE
  }).then(processResponse);
};

export const login = (email, password) => {
  return fetch('/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      email: email,
      password: password
    }),
    credentials: INCLUDE
  }).then(processResponse);
};

export const logout = () => {
  return fetch('/logout', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      [CSRF_TOKEN_HEADER]: Cookies.get(CSRF_TOKEN_COOKIE)
    },
    credentials: INCLUDE
  })
    .then(processResponse)
};

export const register = (username, email, password) => {
  return fetch('/register', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      name: username,
      email: email,
      password: password
    })
  }).then(processResponse);
};
