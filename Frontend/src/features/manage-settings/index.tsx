import { useState } from "react";

import ManageCategories from "features/manage-settings/components/manage-categories";
import ManageNoSnackDays from "features/manage-settings/components/manage-no-snack-days";
import CategoryModal from "features/manage-settings/components/modals/category-modal";
import NoSnackDayModal from "features/manage-settings/components/modals/no-snack-day-modal";
import ShopModal from "features/manage-settings/components/modals/shop-modal";
import SnackModal from "features/manage-settings/components/modals/snack-modal";
import ShopList from "features/manage-settings/components/shop-list";
import SnackList from "features/manage-settings/components/snack-list";

import type {
  CategoryFormDataType,
  CategoryType,
  NoSnackDayFormDataType,
  NoSnackDayType,
  ShopFormDataType,
  ShopType,
  SnackFormDataType,
  SnackType,
} from "features/manage-settings/helpers/manage-setting-types";

const ManageSettings = () => {
  const [shops, setShops] = useState<ShopType[]>([
    {
      id: "1",
      name: "Quality Bakers",
      address: "123 Main St",
      contactDetails: "9876543210",
      paymentMode: "Cash Payment",
      notes: "Best quality bakers in town",
    },
    {
      id: "2",
      name: "KR Bakers",
      address: "456 Market Rd",
      contactDetails: "9876543211",
      paymentMode: "UPI",
      notes: "Fresh items daily",
    },
    {
      id: "3",
      name: "Cake hut",
      address: "789 Baker St",
      contactDetails: "9876543212",
      paymentMode: "Bank Transfer",
      notes: "Specializes in cakes",
    },
  ]);

  const [snacks, setSnacks] = useState<SnackType[]>([
    {
      id: "1",
      name: "Egg Puffs",
      shop: "Quality Bakers",
      pricePerPiece: "15",
      category: "Bakery Items",
    },
    {
      id: "2",
      name: "Parippu Vada",
      shop: "PWD",
      pricePerPiece: "8",
      category: "Bakery Items",
    },
    {
      id: "3",
      name: "Chilly Chicken Roll",
      shop: "KR Bakers",
      pricePerPiece: "25",
      category: "Bakery Items",
    },
  ]);

  const [categories, setCategories] = useState<CategoryType[]>([
    { id: "1", name: "Fruits" },
    { id: "2", name: "Bakery Items" },
    { id: "3", name: "Beverages" },
    { id: "4", name: "Snacks" },
  ]);

  const [noSnackDays, setNoSnackDays] = useState<NoSnackDayType[]>([
    { id: "1", holidayName: "Nowroz Live", date: "2025-08-16" },
    { id: "2", holidayName: "Full office work from Home", date: "2025-08-29" },
  ]);

  // Modal states
  const [shopModalOpen, setShopModalOpen] = useState(false);
  const [snackModalOpen, setSnackModalOpen] = useState(false);
  const [categoryModalOpen, setCategoryModalOpen] = useState(false);
  const [noSnackDayModalOpen, setNoSnackDayModalOpen] = useState(false);

  const [editingShop, setEditingShop] = useState<ShopType | null>(null);
  const [editingSnack, setEditingSnack] = useState<SnackType | null>(null);
  const [editingCategory, setEditingCategory] = useState<CategoryType | null>(
    null
  );
  const [editingNoSnackDay, setEditingNoSnackDay] =
    useState<NoSnackDayType | null>(null);

  // Shop handlers
  const handleAddShop = () => {
    setEditingShop(null);
    setShopModalOpen(true);
  };

  const handleEditShop = (shop: ShopType) => {
    setEditingShop(shop);
    setShopModalOpen(true);
  };

  const handleSaveShop = (data: ShopFormDataType) => {
    if (editingShop) {
      // Edit existing shop
      setShops(
        shops.map((shop) =>
          shop.id === editingShop.id ? { ...shop, ...data } : shop
        )
      );
    } else {
      // Add new shop
      const newShop: ShopType = {
        id: Date.now().toString(),
        ...data,
      };
      setShops([...shops, newShop]);
    }
  };

  const handleDeleteShop = (id: string) => {
    if (confirm("Are you sure you want to delete this shop?")) {
      setShops(shops.filter((shop) => shop.id !== id));
    }
  };

  // Snack handlers
  const handleAddSnack = () => {
    setEditingSnack(null);
    setSnackModalOpen(true);
  };

  const handleEditSnack = (snack: SnackType) => {
    setEditingSnack(snack);
    setSnackModalOpen(true);
  };

  const handleSaveSnack = (data: SnackFormDataType) => {
    if (editingSnack) {
      // Edit existing snack
      setSnacks(
        snacks.map((snack) =>
          snack.id === editingSnack.id ? { ...snack, ...data } : snack
        )
      );
    } else {
      // Add new snack
      const newSnack: SnackType = {
        id: Date.now().toString(),
        ...data,
      };
      setSnacks([...snacks, newSnack]);
    }
  };

  const handleDeleteSnack = (id: string) => {
    if (confirm("Are you sure you want to delete this snack?")) {
      setSnacks(snacks.filter((snack) => snack.id !== id));
    }
  };

  // Category handlers
  const handleAddCategory = () => {
    setEditingCategory(null);
    setCategoryModalOpen(true);
  };

  const handleEditCategory = (id: string) => {
    const category = categories.find((c) => c.id === id);
    if (category) {
      setEditingCategory(category);
      setCategoryModalOpen(true);
    }
  };

  const handleSaveCategory = (data: CategoryFormDataType) => {
    if (editingCategory) {
      // Edit existing category
      setCategories(
        categories.map((category) =>
          category.id === editingCategory.id
            ? { ...category, ...data }
            : category
        )
      );
    } else {
      // Add new category
      const newCategory: CategoryType = {
        id: Date.now().toString(),
        ...data,
      };
      setCategories([...categories, newCategory]);
    }
  };

  const handleDeleteCategory = (id: string) => {
    if (confirm("Are you sure you want to delete this category?")) {
      setCategories(categories.filter((category) => category.id !== id));
    }
  };

  // No snack day handlers
  const handleAddNoSnackDay = () => {
    setEditingNoSnackDay(null);
    setNoSnackDayModalOpen(true);
  };

  const handleEditNoSnackDay = (id: string) => {
    const noSnackDay = noSnackDays.find((d) => d.id === id);
    if (noSnackDay) {
      setEditingNoSnackDay(noSnackDay);
      setNoSnackDayModalOpen(true);
    }
  };

  const handleSaveNoSnackDay = (data: NoSnackDayFormDataType) => {
    if (editingNoSnackDay) {
      // Edit existing no snack day
      setNoSnackDays(
        noSnackDays.map((day) =>
          day.id === editingNoSnackDay.id ? { ...day, ...data } : day
        )
      );
    } else {
      // Add new no snack day
      const newNoSnackDay: NoSnackDayType = {
        id: Date.now().toString(),
        ...data,
      };
      setNoSnackDays([...noSnackDays, newNoSnackDay]);
    }
  };

  const handleDeleteNoSnackDay = (id: string) => {
    if (confirm("Are you sure you want to delete this no snack day?")) {
      setNoSnackDays(noSnackDays.filter((day) => day.id !== id));
    }
  };

  return (
    <>
      <div className="p-6 space-y-8">
        {/* No Snack Days Section */}
        <ManageNoSnackDays
          noSnackDays={noSnackDays}
          onAddNoSnackDay={handleAddNoSnackDay}
          onEditNoSnackDay={handleEditNoSnackDay}
          onDeleteNoSnackDay={handleDeleteNoSnackDay}
        />

        {/* Shop List Section */}
        <ShopList
          shops={shops}
          onAddShop={handleAddShop}
          onEditShop={handleEditShop}
          onDeleteShop={handleDeleteShop}
        />

        {/* Snack List Section */}
        <SnackList
          snacks={snacks}
          onAddSnack={handleAddSnack}
          onEditSnack={handleEditSnack}
          onDeleteSnack={handleDeleteSnack}
        />

        {/* Categories Section */}
        <ManageCategories
          categories={categories}
          onAddCategory={handleAddCategory}
          onEditCategory={handleEditCategory}
          onDeleteCategory={handleDeleteCategory}
        />
      </div>

      {/* Modals */}
      <NoSnackDayModal
        isOpen={noSnackDayModalOpen}
        onClose={() => setNoSnackDayModalOpen(false)}
        onSave={handleSaveNoSnackDay}
        editData={editingNoSnackDay}
      />

      <ShopModal
        isOpen={shopModalOpen}
        onClose={() => setShopModalOpen(false)}
        onSave={handleSaveShop}
        editData={editingShop}
      />

      <SnackModal
        isOpen={snackModalOpen}
        onClose={() => setSnackModalOpen(false)}
        onSave={handleSaveSnack}
        editData={editingSnack}
        shops={shops}
        categories={categories}
      />

      <CategoryModal
        isOpen={categoryModalOpen}
        onClose={() => setCategoryModalOpen(false)}
        onSave={handleSaveCategory}
        editData={editingCategory}
      />
    </>
  );
};

export default ManageSettings;
