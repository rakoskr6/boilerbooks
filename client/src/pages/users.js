import React from 'react'
import { Card } from 'material-ui/Card';
import { Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn } from 'material-ui/Table';
import * as API from '../API.js'
import FloatingActionButton from 'material-ui/FloatingActionButton';
import ContentAdd from 'material-ui/svg-icons/content/add';
import DropDownMenu from 'material-ui/DropDownMenu';
import MenuItem from 'material-ui/MenuItem';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import UserView from '../components/userview.js'

export default class Users extends React.Component {
    state = {
        selectedItem: null,
        category: 0,
        data: []
    }

    componentWillMount() {
        document.title = "Users"
        API.User.search().then(res => {
            this.setState({ data: res })
        })
    }

    rowSelect = (rowNumber, columnId) => {
        this.setState({selectedItem: this.state.data[rowNumber]});
    }

    catSelect = (event, value) => {
        this.setState({category: value})
    }

    closeIndivDialog = () => {
        this.setState({selectedItem: null});
    }

    render() {
        const fabStyle = {
            margin: 0,
            top: 'auto',
            right: 24,
            bottom: 64,
            left: 'auto',
            position: 'fixed',
        }

        return (
            <div>
                <FloatingActionButton secondary={true} style={fabStyle}><ContentAdd /></FloatingActionButton>
                <Card style={{ width: '80%', marginTop: 20, marginLeft: 'auto', marginRight: 'auto' }}>
                    <DropDownMenu value={this.state.category} onChange={this.catSelect} style={{paddingHeight: 8}}>
                        <MenuItem value={0} primaryText="All Users" />
                        <MenuItem value={1} primaryText="My Users" />
                    </DropDownMenu>
                </Card>
                <Card style={{ width: '80%', marginTop: 20, marginLeft: 'auto', marginRight: 'auto' }}>
                    {/*TEMPORARILY SINGLE-SELECT, SHOULD BE MULTI-SELECT LATER*/}
                    <Table
                        fixedHeader={true}
                        selectable={true}
                        multiSelectable={false}
                        onCellClick={this.rowSelect}>
                        <TableHeader
                            displaySelectAll={true}
                            adjustForCheckbox={true}>
                            <TableRow>=
                                <TableHeaderColumn tooltip="Username">Username</TableHeaderColumn>
                                <TableHeaderColumn tooltip="First Name">First Name</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Last Name">Last Name</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Email">Email</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Address">Address</TableHeaderColumn>
                                <TableHeaderColumn tooltip="City">City</TableHeaderColumn>
                                <TableHeaderColumn tooltip="State">State</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Zip">Zip</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody
                            displayRowCheckbox={true}
                            deselectOnClickaway={true}
                            showRowHover={true}>
                            {this.state.data.map((row, index) => (
                                <TableRow key={index}>
                                    <TableRowColumn>{row.username}</TableRowColumn>
                                    <TableRowColumn>{row.first}</TableRowColumn>
                                    <TableRowColumn>{row.last}</TableRowColumn>
                                    <TableRowColumn>{row.email}</TableRowColumn>
                                    <TableRowColumn>{row.address}</TableRowColumn>
                                    <TableRowColumn>{row.city}</TableRowColumn>
                                    <TableRowColumn>{row.state}</TableRowColumn>
                                    <TableRowColumn>{row.zip}</TableRowColumn>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </Card>
                <Dialog
                    title={`${document.title} / View`}
                    actions={[
                        <FlatButton
                            label="Close"
                            primary={true}
                            keyboardFocused={false}
                            onTouchTap={this.closeIndivDialog} />
                    ]}
                    modal={false}
                    open={this.state.selectedItem !== null}
                    onRequestClose={this.closeIndivDialog}
                    autoScrollBodyContent={true}>
                    <UserView user={this.state.selectedItem} />
                </Dialog>
            </div>
        );
    }
}
