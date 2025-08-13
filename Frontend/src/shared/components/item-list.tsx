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
      <div className="flex justify-between items-center mb-3 sm:mb-4">
        <h3 className="text-xl sm:text-2xl font-extrabold text-black">
          {title}
        </h3>
        <button
          onClick={onAdd}
          className="inline-flex items-center px-3 py-2 rounded-lg border-2 border-black bg-yellow-300 text-black font-extrabold text-sm shadow-[2px_2px_0_0_#000] hover:bg-yellow-400"
        >
          + {addButtonText}
        </button>
      </div>

      {/* Items List */}
      <div className="space-y-3">
        {items.map((item) => (
          <div
            key={item.id}
            className="flex items-center justify-between bg-white rounded-lg px-4 py-3 border-2 border-black shadow-[4px_4px_0_0_#000] hover:bg-yellow-50"
          >
            <div className="flex-1">
              <div>
                <span className="font-extrabold text-black">{item.name}</span>
                {item.additionalInfo && (
                  <span className="ml-2 text-sm text-black/70">
                    ({item.additionalInfo})
                  </span>
                )}
              </div>
            </div>

            <div className="flex gap-2 items-center">
              <button
                onClick={() => onEdit(item)}
                className="inline-flex items-center justify-center w-8 h-8 cursor-pointer rounded-md border-2 border-black bg-yellow-300 text-black font-extrabold shadow-[2px_2px_0_0_#000] hover:bg-yellow-400"
                title="Edit"
              >
                <EditIcon />
              </button>
              <button
                onClick={() => onDelete(item.id)}
                className="inline-flex items-center justify-center w-8 h-8 cursor-pointer rounded-md border-2 border-black bg-yellow-300 text-black font-extrabold shadow-[2px_2px_0_0_#000] hover:bg-yellow-400"
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
