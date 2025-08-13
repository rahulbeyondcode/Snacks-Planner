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
      className: "hover:bg-yellow-400",
      title: "Edit",
    },
    {
      icon: <CrossIcon />,
      onClick: (item) => {
        if (confirm("Are you sure you want to delete this category?")) {
          onDeleteCategory(item.id);
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
          Manage Categories
        </h3>
        <button
          onClick={onAddCategory}
          className="inline-flex items-center px-3 py-2 rounded-lg border-2 border-black bg-yellow-300 text-black font-extrabold text-sm shadow-[2px_2px_0_0_#000] hover:bg-yellow-400"
        >
          + Add
        </button>
      </div>

      {/* Data Table */}
      <DataTable
        data={categories}
        columns={columns}
        actions={actions}
        className="w-full overflow-x-auto bg-white rounded-2xl border-2 border-black shadow-[6px_6px_0_0_#000]"
        headerClassName="bg-yellow-200 border-b-2 border-black"
        rowClassName="border-b-2 border-black last:border-b-0 hover:bg-yellow-50"
      />
    </div>
  );
};

export default ManageCategories;
