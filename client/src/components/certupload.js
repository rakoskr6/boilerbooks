import React from 'react';

import {User} from '../API.js';

import UploadZone from './uploadzone.js'

export default class CertUpload extends React.Component {

    state = {
        uploadMessage: "",
        uploading: false
    }

    onDrop = (files) => {
        this.setState({uploading: true});

        User.uploadCert({ username: "mmolo", file: files[0] })
            .then(res => {
                this.setState({uploading: false});

                if (res.error) {
                    this.setState({uploadMessage: `Error: ${res.error}`});
                }
                else {
                    this.setState({uploadMessage: "Upload successful"});
                    if (this.props.onSuccess) {
                        this.props.onSuccess()
                    }
                }
            })
            .catch(err => {
                console.warn(err);
                this.setState({uploading: false});
            })
    }

    render() {
        return (
            <UploadZone
                style={this.props.style}
                uploadMessage={this.state.uploadMessage}
                uploading={this.state.uploading}
                onDrop={this.onDrop}
            >
                <div>Upload Certificate (Drag or click)</div>
            </UploadZone>
        );
    }
}
