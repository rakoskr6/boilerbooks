import React from 'react'
import { Card, CardTitle } from 'material-ui/Card';
import { Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn } from 'material-ui/Table';
import * as API from '../API.js'

export default class Budget extends React.Component {
    state = {
        data: []
    }

    componentWillMount() {
        document.title = "Budget"
        API.Budget.search().then(res => {
            this.setState({
                data: res
            })
        })
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
                        multiSelectable={false}>
                        <TableHeader
                            displaySelectAll={true}
                            adjustForCheckbox={true}>
                            <TableRow>=
                                <TableHeaderColumn tooltip="Organization">Organization</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Name">Name</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Year">Year</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Amount">Amount</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody
                            displayRowCheckbox={true}
                            deselectOnClickaway={true}
                            showRowHover={true}>
                            {this.state.data.map((row, index) => (
                                <TableRow key={index}>
                                    <TableRowColumn>{row.organization}</TableRowColumn>
                                    <TableRowColumn>{row.name}</TableRowColumn>
                                    <TableRowColumn>{row.year}</TableRowColumn>
                                    <TableRowColumn>{row.amount}</TableRowColumn>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </Card>
            </div>
        );
    }
}
