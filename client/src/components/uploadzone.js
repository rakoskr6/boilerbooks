import React from 'react';
import Dropzone from 'react-dropzone';

import FileCloudUpload from 'material-ui/svg-icons/file/cloud-upload';
import Snackbar from 'material-ui/Snackbar';
import CircularProgress from 'material-ui/CircularProgress';


const textColor = "#888888";

const style = {
    width: '100%',
    borderRadius: 2,
    border: 'dashed 2px #eee',
    textAlign: 'center',
    height: '100%',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    color: textColor,
}

const activeStyle = {
    border: 'solid 2px #ddd',
}

export default class UploadZone extends React.Component {
    render() {
        return (
            <div style={this.props.style}>
                <Dropzone ref="dropzone" onDrop={this.props.onDrop} multiple={false} style={style} activeStyle={activeStyle}>
                    {this.props.uploading ?
                        (
                            <CircularProgress />
                        ) : (
                            <div>
                                <FileCloudUpload color={textColor} />
                                <br />
                                <div>{this.props.children}</div>
                            </div>
                    )}
                </Dropzone>
                <Snackbar
                    open={this.props.uploadMessage !== ""}
                    message={this.props.uploadMessage}
                    autoHideDuration={2000}
                    onRequestClose={this.handleSnackbarClose}
                />
            </div>
        );
    }
}
