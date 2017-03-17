import React from 'react'
import { Card } from 'material-ui/Card';
import { Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn } from 'material-ui/Table';
import * as API from '../API.js'
import FloatingActionButton from 'material-ui/FloatingActionButton';
import ContentAdd from 'material-ui/svg-icons/content/add';
import DropDownMenu from 'material-ui/DropDownMenu';
import MenuItem from 'material-ui/MenuItem';

export default class Organizations extends React.Component {
    state = {
        selectedItem: null,
        category: 0,
        data: []
    }

    componentWillMount() {
        document.title = "Organizations"
        API.Organization.search().then(res => {
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
                        <MenuItem value={0} primaryText="All Organizations" />
                        <MenuItem value={1} primaryText="My Organizations" />
                    </DropDownMenu>
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
                                <TableHeaderColumn tooltip="Name">Name</TableHeaderColumn>
                                <TableHeaderColumn tooltip="Parent">Parent</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody
                            displayRowCheckbox={true}
                            deselectOnClickaway={true}
                            showRowHover={true}>
                            {this.state.data.map((row, index) => (
                                <TableRow key={index}>
                                    <TableRowColumn>{row.name}</TableRowColumn>
                                    <TableRowColumn>{row.parent}</TableRowColumn>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </Card>
            </div>
        );
    }
}
