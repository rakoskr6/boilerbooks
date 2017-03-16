import React from 'react'
import {Table, TableBody, TableRow, TableRowColumn} from 'material-ui/Table';
import CertUpload from '../components/certupload.js'

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
                                <TableRowColumn>{key}</TableRowColumn>
                                <TableRowColumn>{this.props.user[key]}</TableRowColumn>
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
                <CertUpload user="me" style={{width: '100%', height: 100}}/>
            </div>
        );
    }
}
