import React from 'react'
import { Card, CardTitle } from 'material-ui/Card';
import { Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn } from 'material-ui/Table';
import * as API from '../API.js'

export default class Purchases extends React.Component {
    state = {
        data: []
    }

    componentWillMount() {
        document.title = "Purchases"
        API.Purchase.search().then(res => {
            this.setState({
                data: res
            });
        });
    }

    rowSelect = (selected) => {
        console.debug(selected.map((index) => {
            return this.state.data[index]
        }))
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
                    onRowSelection={this.rowSelect}>
                    <TableHeader
                        displaySelectAll={true}
                        adjustForCheckbox={true}>
                        <TableRow>
                            <TableHeaderColumn tooltip="ID">ID</TableHeaderColumn>
                            <TableHeaderColumn tooltip="Organization">Organization</TableHeaderColumn>
                            <TableHeaderColumn tooltip="Budget">Budget</TableHeaderColumn>
                            <TableHeaderColumn tooltip="Year">Year</TableHeaderColumn>
                            <TableHeaderColumn tooltip="Purchaser">Purchaser</TableHeaderColumn>
                            <TableHeaderColumn tooltip="Approver">Approver</TableHeaderColumn>
                            <TableHeaderColumn tooltip="Vendor">Vendor</TableHeaderColumn>
                            <TableHeaderColumn tooltip="Cost">Cost</TableHeaderColumn>
                            <TableHeaderColumn tooltip="Date">Date</TableHeaderColumn>
                            <TableHeaderColumn tooltip="Reason">Reason</TableHeaderColumn>
                        </TableRow>
                    </TableHeader>
                    <TableBody
                        displayRowCheckbox={true}
                        deselectOnClickaway={true}
                        showRowHover={true}>
                        {this.state.data.map((row, index) => (
                            <TableRow key={row.purchaseID}>
                                <TableRowColumn>{row.purchaseID}</TableRowColumn>
                                <TableRowColumn>{row.organization}</TableRowColumn>
                                <TableRowColumn>{row.budget}</TableRowColumn>
                                <TableRowColumn>{row.year}</TableRowColumn>
                                <TableRowColumn>{row.username}</TableRowColumn>
                                <TableRowColumn>{row.approvedby}</TableRowColumn>
                                <TableRowColumn>{row.vendor}</TableRowColumn>
                                <TableRowColumn>{row.cost}</TableRowColumn>
                                <TableRowColumn>{row.purchasedate}</TableRowColumn>
                                <TableRowColumn>{row.purchasereason}</TableRowColumn>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </Card>
            </div>
        );
    }
}
