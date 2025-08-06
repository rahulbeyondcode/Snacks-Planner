import CrossIcon from "assets/components/cross-icon";
import EditIcon from "assets/components/edit-icon";
import type { CategoryType } from "features/manage-settings/helpers/manage-setting-types";
import React from "react";
import DataTable, {
  type TableAction,
  type TableColumn,
} from "shared/components/data-table";

type ManageCategoriesProps = {
  categories: CategoryType[];
  onAddCategory: () => void;
  onEditCategory: (id: string) => void;
  onDeleteCategory: (id: string) => void;
};

const ManageCategories: React.FC<ManageCategoriesProps> = ({
  categories,
  onAddCategory,
  onEditCategory,
  onDeleteCategory,
}) => {
  const columns: TableColumn<CategoryType>[] = [
    {
      key: "id",
      title: "Sl No",
      render: (_, __, index) => index + 1,
      className: "w-16",
    },
    {
      key: "name",
      title: "Method",
      className: "font-handwriting",
    },
  ];

  const actions: TableAction<CategoryType>[] = [
    {
      icon: <EditIcon />,
      onClick: (item) => onEditCategory(item.id),
      className: "hover:bg-blue-100 p-1 rounded",
      title: "Edit",
    },
    {
      icon: <CrossIcon />,
      onClick: (item) => {
        if (confirm("Are you sure you want to delete this category?")) {
          onDeleteCategory(item.id);
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
          Manage Categories
        </h3>
        <button
          onClick={onAddCategory}
          className="px-4 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 font-handwriting"
        >
          + Add
        </button>
      </div>

      {/* Data Table */}
      <DataTable
        data={categories}
        columns={columns}
        actions={actions}
        className="overflow-x-auto rounded-lg border border-red-200"
        headerClassName="bg-red-100"
        rowClassName="border-b last:border-b-0"
      />
    </div>
  );
};

export default ManageCategories;
