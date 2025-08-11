import React from "react";

import CrossIcon from "assets/components/cross-icon";
import EditIcon from "assets/components/edit-icon";

export type ItemListItem = {
  id: string;
  name: string;
  additionalInfo?: string;
};

type ItemListProps = {
  title: string;
  items: ItemListItem[];
  onAdd: () => void;
  onEdit: (item: ItemListItem) => void;
  onDelete: (id: string) => void;
  addButtonText?: string;
  className?: string;
};

const ItemList: React.FC<ItemListProps> = ({
  title,
  items,
  onAdd,
  onEdit,
  onDelete,
  addButtonText = "Add",
  className = "",
}) => {
  return (
    <div className={`${className}`}>
      {/* Title and Add Button */}
      <div className="flex justify-between items-center mb-4">
        <h3 className="text-lg font-handwriting text-red-600">{title}</h3>
        <button
          onClick={onAdd}
          className="px-4 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 font-handwriting"
        >
          + {addButtonText}
        </button>
      </div>

      {/* Items List */}
      <div className="space-y-2">
        {items.map((item) => (
          <div
            key={item.id}
            className="flex items-center justify-between bg-orange-200 rounded-lg px-4 py-3"
          >
            <div className="flex-1">
              <div>
                <span className="font-handwriting text-gray-800">
                  {item.name}
                </span>
                {item.additionalInfo && (
                  <span className="ml-2 text-sm text-gray-600 font-handwriting">
                    ({item.additionalInfo})
                  </span>
                )}
              </div>
            </div>

            <div className="flex gap-2">
              <button
                onClick={() => onEdit(item)}
                className="p-1 hover:bg-blue-300 rounded"
                title="Edit"
              >
                <EditIcon />
              </button>
              <button
                onClick={() => onDelete(item.id)}
                className="p-1 hover:bg-red-300 rounded"
                title="Delete"
              >
                <CrossIcon />
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default ItemList;
