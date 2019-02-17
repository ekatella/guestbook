import React from 'react';
import PropTypes from 'prop-types';
import Message from './Message.jsx';
import './Messages.css';

class Messages extends React.Component {
  render() {
    return (
      <div>
        <div className="messages-container">
        {this.props.messages.map(message => {
          return (
              <Message
                key={message.id}
                message={message}
                userId={this.props.userId}
                onReply={this.props.onReplyToMessage}
                onMessageRemove={this.props.onMessageRemove}
                onUpdate={this.props.onUpdate}
              />
          );
        })}
      </div>
      <div className="leave-message">
          <span>LEAVE A MESSAGE</span>
        </div>
      </div>
    );
  }
}

export default Messages;

Messages.defaultProps = {
  messages: []
};

Messages.propTypes = {
  messages: PropTypes.array,
  userId: PropTypes.string,
  onUpdate: PropTypes.func.isRequired,
  onReplyToMessage: PropTypes.func.isRequired,
  onMessageRemove: PropTypes.func.isRequired,
};
