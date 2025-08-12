import React from "react";

import DataTable from "shared/components/data-table";

import type { PaymentModeType } from "features/manage-settings/helpers/manage-setting-types";
import {
  type TableAction,
  type TableColumn,
} from "shared/components/data-table";

import CrossIcon from "assets/components/cross-icon";
import EditIcon from "assets/components/edit-icon";

type ManagePaymentModesProps = {
  paymentModes: PaymentModeType[];
  onAddPaymentMode: () => void;
  onEditPaymentMode: (id: string) => void;
  onDeletePaymentMode: (id: string) => void;
};

const ManagePaymentModes: React.FC<ManagePaymentModesProps> = ({
  paymentModes,
  onAddPaymentMode,
  onEditPaymentMode,
  onDeletePaymentMode,
}) => {
  const columns: TableColumn<PaymentModeType>[] = [
    {
      key: "id",
      title: "Sl No",
      render: (_, __, index) => index + 1,
      className: "w-16",
    },
    {
      key: "name",
      title: "Payment Mode",
      className: "font-handwriting",
    },
  ];

  const actions: TableAction<PaymentModeType>[] = [
    {
      icon: <EditIcon />,
      onClick: (item) => onEditPaymentMode(item.id),
      className: "hover:bg-blue-100 p-1 rounded",
      title: "Edit",
    },
    {
      icon: <CrossIcon />,
      onClick: (item) => {
        if (confirm("Are you sure you want to delete this payment mode?")) {
          onDeletePaymentMode(item.id);
        }
      },
      className: "hover:bg-red-100 p-1 rounded",
      title: "Delete",
    },
  ];

  return (
    <div>
      <div className="flex justify-between items-center mb-4">
        <h3 className="text-lg font-handwriting text-red-600">
          Manage Payment Modes
        </h3>
        <button
          onClick={onAddPaymentMode}
          className="px-4 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 font-handwriting"
        >
          + Add
        </button>
      </div>

      <DataTable
        data={paymentModes}
        columns={columns}
        actions={actions}
        className="overflow-x-auto rounded-lg border border-red-200"
        headerClassName="bg-red-100"
        rowClassName="border-b last:border-b-0"
      />
    </div>
  );
};

export default ManagePaymentModes;
