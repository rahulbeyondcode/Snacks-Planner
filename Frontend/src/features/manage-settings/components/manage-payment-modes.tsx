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
      className: "hover:bg-yellow-400",
      title: "Edit",
    },
    {
      icon: <CrossIcon />,
      onClick: (item) => {
        if (confirm("Are you sure you want to delete this payment mode?")) {
          onDeletePaymentMode(item.id);
        }
      },
      className: "hover:bg-yellow-400",
      title: "Delete",
    },
  ];

  return (
    <div>
      <div className="flex justify-between items-center mb-3 sm:mb-4">
        <h3 className="text-xl sm:text-2xl font-extrabold text-black">
          Manage Payment Modes
        </h3>
        <button
          onClick={onAddPaymentMode}
          className="inline-flex items-center px-3 py-2 rounded-lg border-2 border-black bg-yellow-300 text-black font-extrabold text-sm shadow-[2px_2px_0_0_#000] hover:bg-yellow-400"
        >
          + Add
        </button>
      </div>

      <DataTable
        data={paymentModes}
        columns={columns}
        actions={actions}
        className="w-full overflow-x-auto bg-white rounded-2xl border-2 border-black shadow-[6px_6px_0_0_#000]"
        headerClassName="bg-yellow-200 border-b-2 border-black"
        rowClassName="border-b-2 border-black last:border-b-0 hover:bg-yellow-50"
      />
    </div>
  );
};

export default ManagePaymentModes;
