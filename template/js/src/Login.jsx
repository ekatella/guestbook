import React from 'react';
import PropTypes from 'prop-types';
import './Login.css';

class Login extends React.Component {
  constructor() {
    super();

    this.state = {
      username: '',
      password: ''
    };
  }

  render() {
    return (
      <div>
        <form className="login-container">
          <input
            autoFocus
            autoComplete="off"
            className="login-username"
            value={this.state.username}
            placeholder="username"
            onChange={e => {
              this.setState({ username: e.target.value });
            }}
          />
          <input
            autoComplete="off"
            className="login-password"
            type="password"
            value={this.state.password}
            placeholder="password"
            onChange={e => {
              this.setState({ password: e.target.value });
            }}
          />
          <div className="login-buttons-container">
            <button
              className="login-button"
              onClick={e => {
                e.preventDefault();
                this.props.onLogin(this.state.username, this.state.password);
              }}
            >
              Log In
            </button>
          </div>
        </form>
      </div>
    );
  }
}

export default Login;

Login.propTypes = {
  onLogin: PropTypes.func.isRequired
};
