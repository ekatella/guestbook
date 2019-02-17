import React from 'react';
import PropTypes from 'prop-types';
import './Register.css';

class Register extends React.Component {
  constructor() {
    super();

    this.state = {
      username: '',
      email: '',
      password: ''
    };
  }

  render() {
    return (
      <form className="register-container">
        <input
          autoComplete="off"
          autoFocus
          className="register-user"
          placeholder="e-mail"
          onChange={e => {
            this.setState({ email: e.target.value });
          }}
        />
        <input
          autoComplete="off"
          className="register-user"
          placeholder="username"
          onChange={e => {
            this.setState({ username: e.target.value });
          }}
        />
        <input
          autoComplete="off"
          className="register-password"
          type="password"
          placeholder="password"
          onChange={e => {
            this.setState({ password: e.target.value });
          }}
        />
        <div className="register-buttons-container">
          <button
            onClick={(e) =>{
              e.preventDefault();
              this.props.onRegister(
                this.state.username,
                this.state.email,
                this.state.password
              )
             }
            }
          >
            Register
          </button>
        </div>
      </form>
    );
  }
}

export default Register;

Register.propTypes = {
  onRegister: PropTypes.func.isRequired
};
