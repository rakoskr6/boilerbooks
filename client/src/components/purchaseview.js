import React from 'react'
import {Table, TableBody, TableRow, TableRowColumn} from 'material-ui/Table';

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

export default class PurchaseView extends React.Component {
    render() {
        return (
            <Table selectable={false}>
                <TableBody displayRowCheckbox={false}>
                    {Object.keys(this.props.purchase || {}).map((key) =>
                        <TableRow key={key}>
                            <TableRowColumn>{ucfirst(key)}</TableRowColumn>
                            <TableRowColumn>{this.props.purchase[key]}</TableRowColumn>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        );
    }
}
