import React from 'react'
import { Card } from 'material-ui/Card';
import { Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn } from 'material-ui/Table';
import * as API from '../API.js'
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import PurchaseView from '../components/purchaseview.js';
import FloatingActionButton from 'material-ui/FloatingActionButton';
import ContentAdd from 'material-ui/svg-icons/content/add';
import DropDownMenu from 'material-ui/DropDownMenu';
import MenuItem from 'material-ui/MenuItem';

export default class Purchases extends React.Component {
    state = {
        selectedItem: null,
        category: 0,
        data: []
    }

    componentWillMount() {
        document.title = "Purchases"
        API.Purchase.search().then(res => {
            this.setState({
                data: res
            })
        }).catch(r => console.error(r))
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
                        <MenuItem value={0} primaryText="All Purchases" />
                        <MenuItem value={1} primaryText="My Purchases" />
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
                            <TableRow>
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
                    onRequestClose={this.closeIndivDialog}
                    autoScrollBodyContent={true}>
                    <PurchaseView purchase={this.state.selectedItem} />
                </Dialog>
            </div>
        );
    }
}
