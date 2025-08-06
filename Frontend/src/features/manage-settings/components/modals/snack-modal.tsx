import type {
  CategoryType,
  ShopType,
  SnackFormDataType,
  SnackType,
} from "features/manage-settings/helpers/manage-setting-types";
import React, { useEffect, useState } from "react";
import Modal from "shared/components/modal";

type SnackModalProps = {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: SnackFormDataType) => void;
  editData?: SnackType | null;
  shops: ShopType[];
  categories: CategoryType[];
};

const SnackModal: React.FC<SnackModalProps> = ({
  isOpen,
  onClose,
  onSave,
  editData,
  shops,
  categories,
}) => {
  const [formData, setFormData] = useState<SnackFormDataType>({
    name: "",
    shop: "",
    pricePerPiece: "",
    category: "",
  });

  useEffect(() => {
    if (editData) {
      setFormData({
        name: editData.name,
        shop: editData.shop,
        pricePerPiece: editData.pricePerPiece,
        category: editData.category,
      });
    } else {
      setFormData({
        name: "",
        shop: "",
        pricePerPiece: "",
        category: "",
      });
    }
  }, [editData, isOpen]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (
      formData.name.trim() &&
      formData.shop.trim() &&
      formData.category.trim()
    ) {
      onSave(formData);
      onClose();
    }
  };

  const handleChange = (field: keyof SnackFormDataType, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={editData ? "Edit Snack" : "Add Snack"}
      className="max-w-lg"
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        {/* Snack Name */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Snack Name *
          </label>
          <input
            type="text"
            value={formData.name}
            onChange={(e) => handleChange("name", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            placeholder="Enter snack name"
            required
          />
        </div>

        {/* Shop */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Shop *
          </label>
          <select
            value={formData.shop}
            onChange={(e) => handleChange("shop", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            required
          >
            <option value="">Select shop</option>
            {shops.map((shop) => (
              <option key={shop.id} value={shop.name}>
                {shop.name}
              </option>
            ))}
          </select>
        </div>

        {/* Price per Piece */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Price per Piece
          </label>
          <input
            type="number"
            value={formData.pricePerPiece}
            onChange={(e) => handleChange("pricePerPiece", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            placeholder="Enter price per piece"
            min="0"
            step="0.01"
          />
        </div>

        {/* Category */}
        <div>
          <label className="block text-sm font-handwriting text-gray-700 mb-1">
            Category *
          </label>
          <select
            value={formData.category}
            onChange={(e) => handleChange("category", e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded font-handwriting focus:outline-none focus:border-blue-500"
            required
          >
            <option value="">Select category</option>
            {categories.map((category) => (
              <option key={category.id} value={category.name}>
                {category.name}
              </option>
            ))}
          </select>
        </div>

        {/* Submit Button */}
        <div className="flex justify-end pt-4">
          <button
            type="submit"
            className="px-6 py-2 bg-blue-500 text-white rounded font-handwriting hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Save
          </button>
        </div>
      </form>
    </Modal>
  );
};

export default SnackModal;
