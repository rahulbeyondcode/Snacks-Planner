import React from "react";

interface AddEmployeeModalProps {
  isOpen: boolean;
  onClose: () => void;
  onAdd: (name: string, email: string) => void;
}

const AddEmployeeModal: React.FC<AddEmployeeModalProps> = ({
  isOpen,
  onClose,
  onAdd,
}) => {
  const [name, setName] = React.useState("");
  const [email, setEmail] = React.useState("");

  if (!isOpen) return null;

  const handleAdd = () => {
    if (name && email) {
      onAdd(name, email);
      setName("");
      setEmail("");
      onClose();
    }
  };

  return (
    <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50">
      <div className="bg-white rounded-lg shadow-lg p-6 min-w-[300px]">
        <h3 className="text-lg font-bold mb-4">Add Employee</h3>
        <input
          type="text"
          placeholder="Name"
          className="w-full border rounded px-3 py-2 mb-3"
          value={name}
          onChange={(e) => setName(e.target.value)}
        />
        <input
          type="email"
          placeholder="Email"
          className="w-full border rounded px-3 py-2 mb-3"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />
        <div className="flex justify-end gap-2">
          <button
            onClick={onClose}
            className="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
          >
            Cancel
          </button>
          <button
            onClick={handleAdd}
            className="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600"
          >
            Add
          </button>
        </div>
      </div>
    </div>
  );
};

export default AddEmployeeModal;
