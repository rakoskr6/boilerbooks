import React from 'react'
import {Table, TableBody, TableRow, TableRowColumn} from 'material-ui/Table';


import {User} from '../API.js'

export default class UserView extends React.Component {
    state = { userData: {}, certLink: "" }

    componentDidMount() {
        document.title = "Me"

        const userToGet = this.props.user || this.props.params.user || "";

        User.view({username: userToGet})
            .then(res => this.setState({userData: res.result}))
            .catch(e => console.debug(e));

        this.setState({certLink: User.certificateLink({username: userToGet})});
    }

    render() {
        const rows = Object.keys(this.state.userData).map((key) => {
            return (
                <TableRow key={key}>
                    <TableRowColumn>{key}</TableRowColumn>
                    <TableRowColumn>{this.state.userData[key]}</TableRowColumn>
                </TableRow>
            )
        });

        return (
            <Table selectable={false}>
                <TableBody displayRowCheckbox={false}>
                    {rows}
                    <TableRow key="cert">
                        <TableRowColumn>Certificate</TableRowColumn>
                        <TableRowColumn>
                            <a href={this.state.certLink} target="_blank">Certificate</a>
                        </TableRowColumn>
                    </TableRow>
                </TableBody>
            </Table>
        );
    }
}
