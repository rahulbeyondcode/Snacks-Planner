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
      className: "hover:bg-blue-100 p-1 rounded",
      title: "Edit",
    },
    {
      icon: <CrossIcon />,
      onClick: (item) => {
        if (confirm("Are you sure you want to delete this no snack day?")) {
          onDeleteNoSnackDay(item.id);
        }
      },
      className: "hover:bg-red-100 p-1 rounded",
      title: "Delete",
    },
  ];

  return (
    <div>
      {/* Title and Add Button */}
      <div className="flex justify-between items-center mb-4">
        <h3 className="text-lg font-handwriting text-red-600">
          Manage No Snack Days
        </h3>
        <button
          onClick={onAddNoSnackDay}
          className="px-4 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 font-handwriting"
        >
          + Add
        </button>
      </div>

      {/* Data Table */}
      <DataTable
        data={noSnackDays}
        columns={columns}
        actions={actions}
        className="overflow-x-auto rounded-lg border border-red-200"
        headerClassName="bg-red-100"
        rowClassName="border-b last:border-b-0"
      />
    </div>
  );
};

export default ManageNoSnackDays;
