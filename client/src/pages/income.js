import React from 'react'
import { Card, CardTitle } from 'material-ui/Card';
import { Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn } from 'material-ui/Table';
import * as API from '../API.js'
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import IncomeView from '../components/incomeview.js';

export default class Income extends React.Component {
    state = {
        selectedItem: null,
        data: []
    }

    componentWillMount() {
        document.title = "Income"
        API.Income.search().then(res => {
            this.setState({
                data: res
            })
        })
    }

    rowSelect = (event, rowNumber) => {
        event.preventDefault()
        this.setState({selectedItem: this.state.data[rowNumber]});
    }

    closeIndivDialog = () => {
        this.setState({selectedItem: null});
    }

    render() {
        return (
            <div>
                <Card style={{ width: '80%', marginTop: 20, marginLeft: 'auto', marginRight: 'auto' }}>
                    <CardTitle title={`${document.title} / View All`} style={{paddingBottom: 4}} />
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
                            <TableRow>
                                <TableHeaderColumn tooltip="ID">ID</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Organization">Organization</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Year">Year</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Username">Username</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Source">Source</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Type">Type</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Amount">Amount</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Item">Item</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Status">Status</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Comments">Comments</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Date">Date</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody
                            displayRowCheckbox={true}
                            deselectOnClickaway={true}
                            showRowHover={true}>
                            {this.state.data.map((row, index) => (
                                <TableRow key={row.incomeid}>
                                    <TableRowColumn>
                                        <FlatButton
                                            label={`View`}
                                            onTouchTap={(ev) => this.rowSelect(ev, index)} />
                                    </TableRowColumn>
                                    <TableRowColumn>{row.organization}</TableRowColumn>
                                    <TableRowColumn>{row.year}</TableRowColumn>
                                    <TableRowColumn>{row.username}</TableRowColumn>
                                    <TableRowColumn>{row.source}</TableRowColumn>
                                    <TableRowColumn>{row.type}</TableRowColumn>
                                    <TableRowColumn>{row.amount}</TableRowColumn>
                                    <TableRowColumn>{row.item}</TableRowColumn>
                                    <TableRowColumn>{row.status}</TableRowColumn>
                                    <TableRowColumn>{row.comments}</TableRowColumn>
                                    <TableRowColumn>{row.modify}</TableRowColumn>
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
                            keyboardFocused={true}
                            onTouchTap={this.closeIndivDialog} />
                    ]}
                    modal={false}
                    open={this.state.selectedItem !== null}
                    onRequestClose={this.closeIndivDialog}>
                    <IncomeView income={this.state.selectedItem} />
                </Dialog>
            </div>
        );
    }
}
