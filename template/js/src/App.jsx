import React from 'react';
import ReactDOM from 'react-dom';
import Messages from './Messages.jsx';
import Login from './Login.jsx';
import Register from './Register.jsx';
import * as api from './api';
import { AUTH_STORAGE_KEY } from './constants';
import * as validators from './validators';
import './App.css';

class App extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      showLogin: false,
      showRegister: false,
      mainMessageText: '',
      messages: [],
      isLoggedIn: false,
      error: '',
      userId: '',
      username: ''
    };

    this.login = this.login.bind(this);
    this.logout = this.logout.bind(this);
    this.submitMainMessage = this.submitMainMessage.bind(this);
    this.replyToMessage = this.replyToMessage.bind(this);
    this.removeMessage = this.removeMessage.bind(this);
    this.register = this.register.bind(this);
    this.processError = this.processError.bind(this);
    this.submitMessage = this.submitMessage.bind(this);
    this.updateMessage = this.updateMessage.bind(this);
    this.clearUserData = this.clearUserData.bind(this);
  }

  componentDidMount() {
    const user = JSON.parse(localStorage.getItem(AUTH_STORAGE_KEY));

    if (user && user.username && user.userId) {
      this.setState({
        isLoggedIn: true,
        username: user.username,
        userId: user.userId
      });
    }

    api.getMessages(this.state.isLoggedIn).then(res => {
      if (res && res.messages && res.messages.length > 0) {
        this.setState({ messages: res.messages });
      }
    });
  }

  processError(error) {
    if (error && error.status === 401) {
      this.clearUserData();
      this.setState({ error: 'Please login to perform the operation' })
    }
  }

  clearUserData() {
    this.setState({
      isLoggedIn: false,
      showLogin: true,
      username: '',
      userId: '',
      mainMessageText: '',
    });

    localStorage.removeItem(AUTH_STORAGE_KEY);
  }

  logout() {
    api.logout().then(() => {
      this.clearUserData();
    }).catch(this.processError);
  }

  login(email, password) {
    if (email && password) {
      api.login(email, password).then(res => {
          this.setState({
            isLoggedIn: true,
            showLogin: false,
            showRegister: false,
            username: res.name,
            userId: res.id,
            error: ''
          });

          localStorage.setItem(
            AUTH_STORAGE_KEY,
            JSON.stringify({ username: res.name, userId: res.id })
          );
        }).catch((error) => {
          this.setState({
            error: error.errors.join(', ')
          });
        });
    }
  }

  removeMessage(id) {
    api
      .removeMessage(id)
      .then(() => {
        const newMessages = this.state.messages.filter(msg => msg.id !== id);
        this.setState({ messages: newMessages });
      })
      .catch(this.processError);
  }

  submitMessage(text, parentId) {
    if (text) {
      api
      .addMessage(text, parentId)
      .then(res => {
        const newMessage = {
          id: res.id,
          text,
          created: new Date().toLocaleString(),
          parent_id: parentId,
          user: {
            id: this.state.userId,
            name: this.state.username
          }
        };

        const newMessages = [newMessage].concat(this.state.messages);
        this.setState({ messages: newMessages, mainMessageText: '' });
      })
      .catch(this.processError);
    }
  }

  submitMainMessage() {
    this.submitMessage(this.state.mainMessageText);
  }

  replyToMessage(text, id) {
    this.submitMessage(text, id);
  }

  updateMessage(id, text) {
    if (text) {
      api.updateMessage(id, text).then(() => {
        const messages = this.state.messages.map(msg => {
          if (msg.id === id) {
            return {
              ...msg,
              text
            };
          } else {
            return msg;
          }
        });
  
        this.setState({ messages });
      }).catch(this.processError);
    }
  }

  register(username, email, password) {
    if (!validators.validateEmail(email)) {
      this.setState({ error: 'email is not valid' });
    } else if (!validators.validateName(username)) {
      this.setState({ error: 'username is not valid' });
    } else if (!validators.validatePassword(password)) {
      this.setState({ error: 'Password should be minimum 6 letters, at least one capital letter and one number' });
    } else {
      api.register(username, email, password).then(() => {
          this.setState({
            showLogin: true,
            showRegister: false,
            error: ''
          });
      }).catch((error) => {
        if (error.errors) {
          this.setState({
            error: error.errors.join(', ')
          });
        }
      })
    }
  }

  render() {
    const messages = this.state.messages.map(message => {
      const replies = this.state.messages.filter(
        msg => msg.parent_id === message.id
      );
      return {
        ...message,
        replies
      };
    });

    return (
      <div>
        <div className="title-container">GUESTBOOK</div>
        {this.state.userId && (
          <div className="logout-container">
            <span className="action-button" onClick={this.logout}>
              Logout
            </span>
          </div>
        )}
        <div className="message-container">
          <Messages
            messages={messages}
            onReplyToMessage={this.replyToMessage}
            onMessageRemove={this.removeMessage}
            userId={this.state.userId}
            onUpdate={this.updateMessage}
          />
          {this.state.isLoggedIn && (
            <div className="main-message-container">
              <textarea
                value={this.state.mainMessageText}
                onChange={e => {
                  this.setState({ mainMessageText: e.target.value });
                }}
                autoFocus
                className="main-message"
              />
              <span
                className="action-button"
                onClick={() => {
                  this.submitMainMessage();
                }}
              >
                Comment
              </span>
            </div>
          )}
          {<div className="error-container">{this.state.error}</div>}
          {!this.state.isLoggedIn && (
            <div>
              <div className="not-logged-in">
                <span>You must&nbsp;</span>
                <span
                  className="action-button"
                  onClick={() =>
                    this.setState({ showLogin: true, showRegister: false })
                  }
                >
                  Log In
                </span>
                &nbsp; or &nbsp;
                <span
                  className="action-button"
                  onClick={() =>
                    this.setState({ showRegister: true, showLogin: false })
                  }>
                  Register
                </span>
                <span>&nbsp; in order to comment</span>
              </div>
            </div>
          )}
          {this.state.showLogin && <Login onLogin={this.login} />}
          {this.state.showRegister && <Register onRegister={this.register} />}
        </div>
      </div>
    );
  }
}

ReactDOM.render(<App />, document.getElementById('root'));
