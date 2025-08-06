import type { SnackType } from "features/manage-settings/helpers/manage-setting-types";
import React from "react";
import ItemList from "shared/components/item-list";

type SnackListProps = {
  snacks: SnackType[];
  onAddSnack: () => void;
  onEditSnack: (snack: SnackType) => void;
  onDeleteSnack: (id: string) => void;
};

const SnackList: React.FC<SnackListProps> = ({
  snacks,
  onAddSnack,
  onEditSnack,
  onDeleteSnack,
}) => {
  const snackItems = snacks.map((snack) => ({
    id: snack.id,
    name: snack.name,
    additionalInfo: `from ${snack.shop} - ₹${snack.pricePerPiece}`,
  }));

  const handleEdit = (item: {
    id: string;
    name: string;
    additionalInfo?: string;
  }) => {
    const snack = snacks.find((s) => s.id === item.id);
    if (snack) {
      onEditSnack(snack);
    }
  };

  return (
    <ItemList
      title="Manage Snack List (Only snacks you added will be listed)"
      items={snackItems}
      onAdd={onAddSnack}
      onEdit={handleEdit}
      onDelete={onDeleteSnack}
      addButtonText="Add Snack"
    />
  );
};

export default SnackList;
