import React from 'react'
import {Table, TableBody, TableRow, TableRowColumn} from 'material-ui/Table';
import CertUpload from '../components/certupload.js'
import TextField from 'material-ui/TextField'

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

export default class UserView extends React.Component {
    /*state = { certLink: "" }
    componentDidMount() {
        this.setState({
            certLink: API.User.certificateLink({username: userToGet})
        });
    }*/

    render() {
        return (
            <div>
                <Table selectable={false}>
                    <TableBody displayRowCheckbox={false}>
                        {Object.keys(this.props.user || {}).map((key) =>
                            <TableRow key={key}>
                                <TableRowColumn style={{width:'20%'}}>{ucfirst(key)}</TableRowColumn>
                                <TableRowColumn>
                                    {this.props.editable !== false ?
                                        <TextField
                                        disabled={true}
                                        multiLine={true}
                                        fullWidth={true}
                                        underlineShow={false}
                                        style={{cursor: 'text'}}
                                        textareaStyle={{color: 'black'}}
                                        id={this.props.user[key]}
                                        value={this.props.user[key]} />
                                    :
                                        <TextField
                                        multiLine={true}
                                        fullWidth={true}
                                        hintText={key}
                                        id={this.props.user[key]}
                                        defaultValue={this.props.user[key]} />
                                    }
                                </TableRowColumn>
                            </TableRow>
                        )}
                        <TableRow key="cert">
                            <TableRowColumn>Certificate</TableRowColumn>
                            <TableRowColumn>
                                <a href="#" target="_blank">Certificate</a>
                            </TableRowColumn>
                        </TableRow>
                    </TableBody>
                </Table>
                <CertUpload user={this.props.user} style={{width: '100%', height: 96}}/>
            </div>
        );
    }
}
