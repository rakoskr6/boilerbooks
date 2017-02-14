import React from 'react'
import {Table, TableBody, TableRow, TableRowColumn} from 'material-ui/Table';

export default class PurchaseView extends React.Component {
    render() {
        return (
            <Table selectable={false}>
                <TableBody displayRowCheckbox={false}>
                    {Object.keys(this.props.purchase || {}).map((key) =>
                        <TableRow key={key}>
                            <TableRowColumn>{key}</TableRowColumn>
                            <TableRowColumn>{this.props.purchase[key]}</TableRowColumn>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        );
    }
}
