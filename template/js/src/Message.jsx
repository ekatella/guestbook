import React from 'react';
import PropTypes from 'prop-types';
import { AUTH_STORAGE_KEY } from './constants';
import './Message.css';

class Message extends React.Component {
  constructor(props) {
    super(props);

    const user = JSON.parse(localStorage.getItem(AUTH_STORAGE_KEY));
    this.state = {
      showReplyMessage: false,
      messageText: '',
      userId: user ? user.userId : '',
      editingMessageId: false,
      textInEdit: ''
    };
  }

  render() {
    if (this.props.message.parent_id) {
      return null;
    }

    if (this.state.editingMessageId) {
      return (
        <div className="message-update-container">
          <textarea
            value={this.state.textInEdit}
            onChange={e => {
              this.setState({ textInEdit: e.target.value });
            }}
            autoFocus
            className="reply-text"
          />
          <span
            className="action-button"
            onClick={() => {
              this.setState({ editingMessageId: '', textInEdit: '' });
            }}
          >
            cancel
          </span>
          <span className="message-update action-button" onClick={() => {
              this.props.onUpdate(this.state.editingMessageId, this.state.textInEdit);
              this.setState({ editingMessageId: '', textInEdit: '' });
            }}>
            update
          </span>
        </div>
      );
    }

    return (
      <div>
        <div className="message">
          <span>{this.props.message.text}</span>
          <div className="message-action-container">
            {this.props.userId && (
              <span
                className="reply-button action-button"
                onClick={() => this.setState({ showReplyMessage: true })}
              >
                reply
              </span>
            )}
            {this.props.userId === this.props.message.user.id && (
              <div className="message-actions-container">
                <span
                  className="message-edit action-button"
                  onClick={() => {
                    this.setState({
                      editingMessageId: this.props.message.id,
                      textInEdit: this.props.message.text
                    });
                  }}
                >
                  edit
                </span>
                <span
                  className="message-remove action-button"
                  onClick={() => {
                    this.props.onMessageRemove(this.props.message.id);
                  }}
                >
                  remove
                </span>
              </div>
            )}
          </div>
          <span className="commented-by">
            <span>Commented by {this.props.message.user.name} on &nbsp;
              {this.props.message.created}</span>
          </span>
        </div>
        {this.props.message.replies && (
          <div>
            {this.props.message.replies.map(subMessage => {
              return (
                <div className="submessage" key={subMessage.id}>
                  <div>
                    <div>
                      <span>{subMessage.text}</span>
                    </div>
                    <span className="submessage-replied-by">
                      Replied by {subMessage.user.name} on {subMessage.created}
                    </span>
                  </div>
                  {this.props.userId === subMessage.user.id && (
                    <div className="message-actions-container">
                      <span
                        className="message-edit action-button"
                        onClick={() => {
                          this.setState({
                            editingMessageId: subMessage.id,
                            textInEdit: subMessage.text
                          });
                        }}
                      >
                        edit
                      </span>
                      <span
                        className="submessage-remove action-button"
                        onClick={() => {
                          this.props.onMessageRemove(subMessage.id);
                          this.setState({ showReplyMessage: false });
                        }}
                      >
                        remove
                      </span>
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        )}
        <div>
          {this.state.showReplyMessage && (
            <div>
              <textarea
                autoFocus
                value={this.state.messageText}
                onChange={e => {
                  this.setState({ messageText: e.target.value });
                }}
                className="reply-text"
              />
              <div className="actions-buttons-container">
                <span
                  className="cancel-button action-button"
                  onClick={() => this.setState({ showReplyMessage: false })}
                >
                  Cancel
                </span>
                <span
                  className="action-button"
                  onClick={() => {
                    this.props.onReply(
                      this.state.messageText,
                      this.props.message.id
                    );
                    this.setState({ messageText: '' });
                  }}
                >
                  Comment
                </span>
              </div>
            </div>
          )}
        </div>
      </div>
    );
  }
}

export default Message;

Message.propTypes = {
  message: PropTypes.object,
  userId: PropTypes.string,
  onReply: PropTypes.func.isRequired,
  onUpdate: PropTypes.func.isRequired,
  onMessageRemove: PropTypes.func.isRequired
};
