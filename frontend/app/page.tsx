"use client";

import { Button } from "@/components/ui/button";
import UserTable from "@/components/user-table";
import { StandardResponse } from "@/interface/standard-response";
import { getRequest } from "@/lib/utils";
import Link from "next/link";
import { useEffect, useState } from "react";

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

export default function Home() {
  const [userData, setUserData] = useState<User[]>([]);
  const [loading, setLoading] = useState<boolean>(true);

  async function users() {
    try {
      const data: StandardResponse = await getRequest(USERS_URL);
      setUserData(data.data);
      setLoading(false);
    } catch (error) {
      console.error(error);
    }
  }

  useEffect(() => {
    users();
  }, []);

  return (
    <section className="container mx-auto p-6 font-mono flex flex-col space-y-4">
      <div className="flex justify-end">
        <Link href={"add-user"}>
          <Button>Add User</Button>
        </Link>
      </div>

      <UserTable />
    </section>
  );
}
