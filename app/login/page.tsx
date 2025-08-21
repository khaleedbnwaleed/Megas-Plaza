"use client"

import type React from "react"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Label } from "@/components/ui/label"
import { Alert, AlertDescription } from "@/components/ui/alert"
import { Eye, EyeOff, User, Lock, Info } from "lucide-react"
import Link from "next/link"

export default function LoginPage() {
  const [showPassword, setShowPassword] = useState(false)
  const [formData, setFormData] = useState({
    email: "",
    password: "",
  })

  const demoAccounts = [
    { role: "Super Admin", email: "admin@megaplaza.com", password: "admin123" },
    { role: "Manager", email: "manager@megaplaza.com", password: "manager123" },
    { role: "Tenant", email: "tenant@megaplaza.com", password: "tenant123" },
  ]

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    // Demo login logic
    const account = demoAccounts.find((acc) => acc.email === formData.email && acc.password === formData.password)
    if (account) {
      alert(`Login successful as ${account.role}! Redirecting to dashboard...`)
    } else {
      alert("Invalid credentials. Please use one of the demo accounts.")
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
      <div className="w-full max-w-md space-y-6">
        <div className="text-center">
          <h1 className="text-3xl font-bold text-gray-900">Mega School Plaza</h1>
          <p className="text-gray-600 mt-2">Shop Rental Management System</p>
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="w-5 h-5" />
              Sign In
            </CardTitle>
            <CardDescription>Enter your credentials to access your account</CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="email">Email Address</Label>
                <Input
                  id="email"
                  type="email"
                  placeholder="Enter your email"
                  value={formData.email}
                  onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                  required
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="password">Password</Label>
                <div className="relative">
                  <Input
                    id="password"
                    type={showPassword ? "text" : "password"}
                    placeholder="Enter your password"
                    value={formData.password}
                    onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                    required
                  />
                  <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                    onClick={() => setShowPassword(!showPassword)}
                  >
                    {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                  </Button>
                </div>
              </div>

              <Button type="submit" className="w-full">
                <Lock className="w-4 h-4 mr-2" />
                Sign In
              </Button>
            </form>

            <div className="mt-4 text-center">
              <Link href="/register" className="text-sm text-blue-600 hover:underline">
                Don't have an account? Register here
              </Link>
            </div>
          </CardContent>
        </Card>

        <Alert>
          <Info className="w-4 h-4" />
          <AlertDescription>
            <strong>Demo Accounts Available:</strong>
            <div className="mt-2 space-y-1 text-sm">
              {demoAccounts.map((account, index) => (
                <div key={index} className="flex justify-between">
                  <span className="font-medium">{account.role}:</span>
                  <span>
                    {account.email} / {account.password}
                  </span>
                </div>
              ))}
            </div>
          </AlertDescription>
        </Alert>
      </div>
    </div>
  )
}
