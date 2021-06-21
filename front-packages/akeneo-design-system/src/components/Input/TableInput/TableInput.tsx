import React, {ReactNode} from 'react';
import styled from 'styled-components';
import {Override} from '../../../shared';
import {TableInputHeader} from './TableInputHeader/TableInputHeader';
import {TableInputHeaderCell} from './TableInputHeaderCell/TableInputHeaderCell';
import {TableInputBody} from './TableInputBody/TableInputBody';
import {TableInputCell} from './TableInputCell/TableInputCell';
import {TableInputRow} from './TableInputRow/TableInputRow';
import {TableInputTextInput} from './TableInputTextInput/TableInputTextInput';
import {TableInputNumberInput} from './TableInputNumberInput/TableInputNumberInput';

const TableInputContainer = styled.div`
  width: 100%;
  overflow: auto;
`;

const TableInputTable = styled.table`
  border-spacing: 0;
  width: 100%;

  & th:first-child,
  & tr > td:first-child {
    transition: box-shadow 0.15s;
  }
  &.shadowed th:first-child {
    box-shadow: rgba(0, 0, 0, 0.2) 0px 7.5px 15px 0px;
  }
  &.shadowed tr > td:first-child {
    box-shadow: rgba(0, 0, 0, 0.2) 0px 15px 15px 0px;
  }
`;

type TableInputProps = Override<
  React.HTMLAttributes<HTMLTableElement>,
  {
    /**
     * TODO.
     */
    children?: ReactNode;
  }
>;

/**
 * TODO.
 */
const TableInput = ({children, ...rest}: TableInputProps) => {
  const [shadowed, setShadowed] = React.useState<boolean>(false);
  const handleScroll = (event: React.UIEvent<HTMLElement>) => {
    setShadowed(event.currentTarget.scrollLeft > 0);
  };
  return (
    <TableInputContainer onScroll={handleScroll} {...rest}>
      <TableInputTable className={shadowed ? 'shadowed' : ''}>{children}</TableInputTable>
    </TableInputContainer>
  );
};

TableInput.Header = TableInputHeader;
TableInput.HeaderCell = TableInputHeaderCell;
TableInput.Body = TableInputBody;
TableInput.Row = TableInputRow;
TableInput.Cell = TableInputCell;
TableInput.TextInput = TableInputTextInput;
TableInput.NumberInput = TableInputNumberInput;

export {TableInput};