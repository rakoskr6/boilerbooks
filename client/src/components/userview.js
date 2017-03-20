import React from 'react'
import {Table, TableBody, TableRow, TableRowColumn} from 'material-ui/Table';
import CertUpload from '../components/certupload.js'
import TextField from 'material-ui/TextField'
import * as API from '../API.js'
import FlatButton from 'material-ui/FlatButton';
import Popover from 'material-ui/Popover'

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1)
}

export default class UserView extends React.Component {
    state = {
        certURL: '',
        openCert: null
    }
    /*state = { certLink: "" }
    componentDidMount() {
        this.setState({
            certLink: API.User.certificateLink({username: userToGet})
        });
    }*/

    componentDidMount() {
        var url = API.Resource.downloadURL({id: this.props.user['cert']});
        this.setState({certURL: url});
    }

    render() {
        return (
            <div>
                <Table selectable={false}>
                    <TableBody displayRowCheckbox={false}>
                        {Object.keys(this.props.user || {}).map((key) =>
                            <TableRow key={key}>
                                <TableRowColumn style={{width:'20%'}}>{ucfirst(key)}</TableRowColumn>
                                <TableRowColumn>
                                    {!this.props.editable
                                    ?    <TextField
                                            disabled={true}
                                            multiLine={true}
                                            fullWidth={true}
                                            underlineShow={false}
                                            style={{cursor: 'text'}}
                                            textareaStyle={{color: 'black'}}
                                            id={this.props.user[key]}
                                            value={this.props.user[key]} />
                                    :   <TextField
                                            onChange={() => window.dispatchEvent(new Event('resize'))}
                                            multiLine={true}
                                            fullWidth={true}
                                            hintText={key}
                                            id={this.props.user[key]}
                                            defaultValue={this.props.user[key]} />
                                    }
                                </TableRowColumn>
                            </TableRow>
                        )}
                        <TableRow key="certificate">
                            <TableRowColumn>Certificate</TableRowColumn>
                            <TableRowColumn>
                                <FlatButton
                                    label="View"
                                    primary={false}
                                    keyboardFocused={false}
                                    onTouchTap={(event) => this.setState({openCert: event.currentTarget})} />
                            </TableRowColumn>
                        </TableRow>
                    </TableBody>
                </Table>
                {this.props.editable
                    ? <CertUpload user={this.props.user} style={{width: '100%', height: 96}} />
                    : <div />
                }
                <Popover
                  open={this.state.openCert != null}
                  anchorEl={this.state.openCert}
                  anchorOrigin={{horizontal: 'middle', vertical: 'center'}}
                  targetOrigin={{horizontal: 'middle', vertical: 'center'}}
                  onRequestClose={(event) => this.setState({openCert: null})}>
                    <div>
                        <a href={this.state.certURL} target='_blank'>
                            <img style={{width: 600, height: 'auto'}}
                                 src={this.state.certURL} alt="Certificate" />
                        </a>
                    </div>
                </Popover>
            </div>
        );
    }
}
