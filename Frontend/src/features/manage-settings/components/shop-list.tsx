import React from "react";

import ItemList from "shared/components/item-list";

import type { ShopType } from "features/manage-settings/helpers/manage-setting-types";

type ShopListProps = {
  shops: ShopType[];
  onAddShop: () => void;
  onEditShop: (shop: ShopType) => void;
  onDeleteShop: (id: string) => void;
};

const ShopList: React.FC<ShopListProps> = ({
  shops,
  onAddShop,
  onEditShop,
  onDeleteShop,
}) => {
  const shopItems = shops.map((shop) => ({
    id: shop.id,
    name: shop.name,
    additionalInfo: shop.paymentMode,
  }));

  const handleEdit = (item: {
    id: string;
    name: string;
    additionalInfo?: string;
  }) => {
    const shop = shops.find((s) => s.id === item.id);
    if (shop) {
      onEditShop(shop);
    }
  };

  return (
    <ItemList
      title="Manage Shop List (Only shops you added will be listed)"
      items={shopItems}
      onAdd={onAddShop}
      onEdit={handleEdit}
      onDelete={onDeleteShop}
      addButtonText="Add Shop"
    />
  );
};

export default ShopList;
