"use client";

import { Button } from "@/components/ui/button";
import { UserTable } from "@/components/user-table";
import Link from "next/link";
import { useState } from "react";

export default function Home() {
  return (
    <section className="container mx-auto font-mono flex flex-col space-y-4">
      <div className="flex justify-end">
        <Link href={"add-user"}>
          <Button>Add User</Button>
        </Link>
      </div>

      <UserTable />
    </section>
  );
}
