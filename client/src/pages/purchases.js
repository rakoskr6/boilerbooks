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
import Popover from 'material-ui/Popover'
import Subheader from 'material-ui/Subheader'
import Snackbar from 'material-ui/Snackbar'

export default class Purchases extends React.Component {
    state = {
        selectedItem: null,
        editable: false,
        category: 0,
        confirmDelete: null,
        userMsg: null,
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
        this.setState({selectedItem: this.state.data[rowNumber], editable: false})
    }

    catSelect = (event, value) => {
        this.setState({category: value, editable: false})
    }

    closeIndivDialog = () => {
        this.setState({selectedItem: null, editable: false, confirmDelete: null})
    }

    // Edit mode allows individual item changes; should be disabled when
    // the item is switched.
    enterEditMode = () => {
        this.setState({editable: true})
    }
    saveChanges = () => {
        this.setState({editable: false, userMsg: "Item saved."});
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
                            displaySelectAll={false}
                            adjustForCheckbox={false}>
                            <TableRow>
                                <TableHeaderColumn tooltip="Date">Date</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Organization">Organization</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Budget">Budget</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Year">Year</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Purchaser">Purchaser</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Approver">Approver</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Item">Item</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Cost">Cost</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody
                            displayRowCheckbox={false}
                            deselectOnClickaway={true}
                            showRowHover={true}>
                            {this.state.data.map((row, index) => (
                                <TableRow key={row.purchaseID}>
                                    <TableRowColumn>{row.purchasedate}</TableRowColumn>
                                    <TableRowColumn>{row.organization}</TableRowColumn>
                                    <TableRowColumn>{row.budget}</TableRowColumn>
                                    <TableRowColumn>{row.year}</TableRowColumn>
                                    <TableRowColumn>{row.username}</TableRowColumn>
                                    <TableRowColumn>{row.approvedby}</TableRowColumn>
                                    <TableRowColumn>{row.item}</TableRowColumn>
                                    <TableRowColumn>{row.cost}</TableRowColumn>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </Card>
                <Dialog
                    title={`${document.title} / ` + (this.state.editable ? 'Edit' : 'View')}
                    actions={[
                        <FlatButton
                            label="Delete"
                            primary={false}
                            keyboardFocused={false}
                            backgroundColor="#E53935"
                            hoverColor="#B71C1C"
                            labelStyle={{color: 'white'}}
                            onTouchTap={(event) => this.setState({confirmDelete: event.currentTarget})} />,

                        !this.state.editable ?
                        <FlatButton
                            label="Edit"
                            primary={false}
                            keyboardFocused={false}
                            onTouchTap={this.enterEditMode} />
                        :
                        <FlatButton
                            label="Save"
                            primary={false}
                            keyboardFocused={false}
                            onTouchTap={this.saveChanges} />,

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
                    <PurchaseView purchase={this.state.selectedItem} editable={this.state.editable} />
                </Dialog>
                <Popover
                  open={this.state.confirmDelete != null}
                  anchorEl={this.state.confirmDelete}
                  style={{overflowY: 'auto', backgroundColor: '#B71C1C'}}
                  anchorOrigin={{horizontal: 'middle', vertical: 'center'}}
                  targetOrigin={{horizontal: 'middle', vertical: 'center'}}
                  onRequestClose={(event) => this.setState({confirmDelete: null})}>
                    <center style={{padding: 16}}>
                        <Subheader style={{margin: 0, padding: 0, color: 'white'}}>
                            Are you sure you want to delete this item?
                        </Subheader>
                        <FlatButton
                            label="Yes, I would like to delete this item."
                            primary={true}
                            keyboardFocused={true}
                            fullWidth={true}
                            backgroundColor="#B71C1C"
                            hoverColor="#E53935"
                            labelStyle={{color: 'white'}}
                            onTouchTap={() => {
                                this.closeIndivDialog()
                                this.setState({userMsg: "Item deleted."})
                            }} />
                    </center>
                </Popover>
                <Snackbar
                    open={this.state.userMsg != null}
                    message={this.state.userMsg || ''}
                    autoHideDuration={3000}
                    onRequestClose={() => this.setState({userMsg: null})} />
            </div>
        );
    }
}
