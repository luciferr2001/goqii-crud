import { StandardResponse } from "@/interface/standard-response";
import { getRequest } from "@/lib/utils";
import { Edit, Trash } from "lucide-react";
import Link from "next/link";
import React, { useEffect, useState } from "react";

interface User {
  id: string;
  first_name: string;
  last_name: string;
  email: string;
  phone_number: string;
  dob: string;
  added_on: string;
  updated_on: string;
  status: string; // Or `number` based on your use case
}

const USERS_URL = "http://localhost:8080/v1/user";

type DynamicFormProps = {
  refresh: number;
};

export const UserTable: React.FC<DynamicFormProps> = ({ refresh }) => {
  const [userData, setUserData] = useState<User[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  
  useEffect(() => {
    setLoading(true);
    const fetchData = async () => {
      const data: StandardResponse = await getRequest(USERS_URL);
      setUserData(data.data);
      setLoading(false);
    };
    fetchData();
  }, [refresh]);

  if (loading) {
    return (
      <div className="flex justify-center items-center w-full h-64">
        <div className="border-t-4 border-gray-500 border-solid rounded-full w-8 h-8 animate-spin"></div>
      </div>
    );
  }

  return (
    <div className="w-full mb-8 overflow-hidden rounded-lg shadow-lg">
      <div className="w-full overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="text-md font-semibold tracking-wide text-left text-gray-900 bg-gray-100 uppercase border-b border-gray-600">
              <th className="px-4 py-3">Name</th>
              <th className="px-4 py-3">Phone Number</th>
              <th className="px-4 py-3">Email</th>
              <th className="px-4 py-3">DOB</th>
              <th className="px-4 py-3">Status</th>
              <th className="px-4 py-3">Action</th>
            </tr>
          </thead>
          <tbody className="bg-white">
            {userData.map((user, index) => (
              <tr key={index} className="text-gray-700">
                <td className="px-4 py-3">
                  <div className="flex items-center text-sm">
                    <p className="text-xs text-gray-600">
                      {`${user.first_name} ${user.last_name}`}
                    </p>
                  </div>
                </td>
                <td className="px-4 py-3 text-ms font-semibold">
                  {user.phone_number}
                </td>
                <td className="px-4 py-3 text-xs">
                  <span className="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-sm">
                    {user.email}
                  </span>
                </td>
                <td className="px-4 py-3 text-sm">{user.dob}</td>
                <td className="px-4 py-3 text-xs">
                  <span className="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-sm">
                    {user.status === "1" ? "Active" : "Inactive"}
                  </span>
                </td>
                <td className="px-4 py-3 flex space-x-2">
                  <Link href={`edit-user/${encodeURIComponent(user.id)}`}>
                    <Edit className="text-blue-600 hover:text-blue-800" size={16} />
                  </Link>
                  <button className="text-red-600 hover:text-red-800">
                    <Trash size={16} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};
