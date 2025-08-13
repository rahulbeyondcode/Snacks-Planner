import React from "react";

import CrossIcon from "assets/components/cross-icon";
import EditIcon from "assets/components/edit-icon";

import DataTable from "shared/components/data-table";

import type { NoSnackDayType } from "features/manage-settings/helpers/manage-setting-types";
import {
  type TableAction,
  type TableColumn,
} from "shared/components/data-table";

type ManageNoSnackDaysProps = {
  noSnackDays: NoSnackDayType[];
  onAddNoSnackDay: () => void;
  onEditNoSnackDay: (id: string) => void;
  onDeleteNoSnackDay: (id: string) => void;
};

const ManageNoSnackDays: React.FC<ManageNoSnackDaysProps> = ({
  noSnackDays,
  onAddNoSnackDay,
  onEditNoSnackDay,
  onDeleteNoSnackDay,
}) => {
  const formatDate = (dateString: string) => {
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
      });
    } catch {
      return dateString;
    }
  };

  const columns: TableColumn<NoSnackDayType>[] = [
    {
      key: "id",
      title: "Sl No",
      render: (_, __, index) => index + 1,
      className: "w-16",
    },
    {
      key: "date",
      title: "Date",
      render: (value) => formatDate(value),
      className: "font-handwriting",
    },
    {
      key: "holidayName",
      title: "Reason",
      className: "font-handwriting",
    },
  ];

  const actions: TableAction<NoSnackDayType>[] = [
    {
      icon: <EditIcon />,
      onClick: (item) => onEditNoSnackDay(item.id),
      className: "hover:bg-yellow-400",
      title: "Edit",
    },
    {
      icon: <CrossIcon />,
      onClick: (item) => {
        if (confirm("Are you sure you want to delete this no snack day?")) {
          onDeleteNoSnackDay(item.id);
        }
      },
      className: "hover:bg-yellow-400",
      title: "Delete",
    },
  ];

  return (
    <div>
      {/* Title and Add Button */}
      <div className="flex justify-between items-center mb-3 sm:mb-4">
        <h3 className="text-xl sm:text-2xl font-extrabold text-black">
          Manage No Snack Days
        </h3>
        <button
          onClick={onAddNoSnackDay}
          className="inline-flex items-center px-3 py-2 rounded-lg border-2 border-black bg-yellow-300 text-black font-extrabold text-sm shadow-[2px_2px_0_0_#000] hover:bg-yellow-400"
        >
          + Add
        </button>
      </div>

      {/* Data Table */}
      <DataTable
        data={noSnackDays}
        columns={columns}
        actions={actions}
        className="w-full overflow-x-auto bg-white rounded-2xl border-2 border-black shadow-[6px_6px_0_0_#000]"
        headerClassName="bg-yellow-200 border-b-2 border-black"
        rowClassName="border-b-2 border-black last:border-b-0 hover:bg-yellow-50"
      />
    </div>
  );
};

export default ManageNoSnackDays;
