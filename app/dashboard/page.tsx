"use client"

import { useEffect, useState } from "react"
import { useRouter } from "next/navigation"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Progress } from "@/components/ui/progress"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { ChartContainer, ChartTooltip, ChartTooltipContent } from "@/components/ui/chart"
import {
  Line,
  LineChart,
  XAxis,
  YAxis,
  CartesianGrid,
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
  BarChart,
  Bar,
} from "recharts"
import {
  Users,
  Building,
  FileText,
  DollarSign,
  TrendingUp,
  AlertTriangle,
  Clock,
  CheckCircle,
  XCircle,
  LogOut,
  User,
  Send,
  Phone,
  Mail,
  X,
} from "lucide-react"

interface UserData {
  email: string
  role: string
  name: string
}

const getStatusIcon = (status: string) => {
  switch (status) {
    case "pending":
      return <Clock className="w-4 h-4 text-gray-500" />
    case "approved":
      return <CheckCircle className="w-4 h-4 text-green-500" />
    case "rejected":
      return <XCircle className="w-4 h-4 text-red-500" />
    default:
      return null
  }
}

const getStatusColor = (status: string) => {
  switch (status) {
    case "completed":
      return "bg-green-100 text-green-800"
    case "pending":
      return "bg-yellow-100 text-yellow-800"
    case "overdue":
      return "bg-red-100 text-red-800"
    default:
      return ""
  }
}

export default function DashboardPage() {
  const [user, setUser] = useState<UserData | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [activeTab, setActiveTab] = useState<
    "overview" | "shops" | "invoices" | "apply" | "tenants" | "applications" | "payments" | "reports"
  >("overview")
  const [searchTerm, setSearchTerm] = useState("")
  const [selectedFloor, setSelectedFloor] = useState("all")

  const [selectedTenant, setSelectedTenant] = useState<any>(null)
  const [selectedApplication, setSelectedApplication] = useState<any>(null)
  const [showApprovalDialog, setShowApprovalDialog] = useState(false)
  const [approvalAction, setApprovalAction] = useState<"approve" | "reject" | null>(null)

  const router = useRouter()

  const paymentHistoryData = [
    { month: "Aug", amount: 150000, status: "paid" },
    { month: "Sep", amount: 150000, status: "paid" },
    { month: "Oct", amount: 150000, status: "paid" },
    { month: "Nov", amount: 175000, status: "paid" },
    { month: "Dec", amount: 175000, status: "paid" },
    { month: "Jan", amount: 175000, status: "pending" },
  ]

  const paymentStatusData = [
    { name: "Paid", value: 875000, color: "#22c55e" },
    { name: "Pending", value: 175000, color: "#eab308" },
    { name: "Overdue", value: 0, color: "#ef4444" },
  ]

  const monthlyExpensesData = [
    { category: "Rent", amount: 175000 },
    { category: "Utilities", amount: 25000 },
    { category: "Maintenance", amount: 15000 },
    { category: "Insurance", amount: 8000 },
  ]

  const occupancyTrendData = [
    { month: "Aug", occupancy: 82 },
    { month: "Sep", occupancy: 85 },
    { month: "Oct", occupancy: 88 },
    { month: "Nov", occupancy: 85 },
    { month: "Dec", occupancy: 87 },
    { month: "Jan", occupancy: 90 },
  ]

  const adminAnalyticsData = [
    { month: "Aug", revenue: 4200000, occupancy: 82, applications: 15 },
    { month: "Sep", revenue: 4350000, occupancy: 85, applications: 18 },
    { month: "Oct", revenue: 4500000, occupancy: 88, applications: 22 },
    { month: "Nov", revenue: 4280000, occupancy: 85, applications: 16 },
    { month: "Dec", revenue: 4650000, occupancy: 87, applications: 20 },
    { month: "Jan", revenue: 4850000, occupancy: 90, applications: 25 },
  ]

  const tenantsList = [
    {
      id: 1,
      name: "John Smith",
      email: "john@fashionstore.com",
      business: "Fashion Store",
      shop: "A-101",
      rent: 250000,
      status: "active",
      joinDate: "2023-06-15",
      phone: "+234 801 234 5678",
      paymentStatus: "current",
    },
    {
      id: 2,
      name: "Sarah Johnson",
      email: "sarah@coffeecorner.com",
      business: "Coffee Corner",
      shop: "B-205",
      rent: 320000,
      status: "active",
      joinDate: "2023-08-20",
      phone: "+234 802 345 6789",
      paymentStatus: "overdue",
    },
    {
      id: 3,
      name: "Mike Wilson",
      email: "mike@techservices.com",
      business: "Tech Services",
      shop: "C-301",
      rent: 200000,
      status: "active",
      joinDate: "2023-09-10",
      phone: "+234 803 456 7890",
      paymentStatus: "current",
    },
    {
      id: 4,
      name: "Lisa Brown",
      email: "lisa@beautyworld.com",
      business: "Beauty World",
      shop: "A-102",
      rent: 280000,
      status: "pending",
      joinDate: "2024-01-05",
      phone: "+234 804 567 8901",
      paymentStatus: "pending",
    },
  ]

  const pendingApplications = [
    {
      id: 1,
      applicant: "David Chen",
      email: "david@electronics.com",
      business: "Electronics Hub",
      preferredShop: "A-103",
      businessType: "Electronics",
      experience: "5 years in electronics retail",
      submittedDate: "2024-01-20",
      status: "pending",
    },
    {
      id: 2,
      applicant: "Maria Garcia",
      email: "maria@bookstore.com",
      business: "Book Haven",
      preferredShop: "B-201",
      businessType: "Retail",
      experience: "3 years in book retail",
      submittedDate: "2024-01-18",
      status: "pending",
    },
    {
      id: 3,
      applicant: "Ahmed Hassan",
      email: "ahmed@tailoring.com",
      business: "Master Tailors",
      preferredShop: "C-305",
      businessType: "Services",
      experience: "8 years in tailoring services",
      submittedDate: "2024-01-15",
      status: "pending",
    },
  ]

  const allPayments = [
    {
      id: 1,
      tenant: "Fashion Store",
      tenantEmail: "john@fashionstore.com",
      shop: "A-101",
      amount: 250000,
      dueDate: "2024-02-15",
      paidDate: "2024-02-10",
      status: "completed",
      type: "rent",
    },
    {
      id: 2,
      tenant: "Coffee Corner",
      tenantEmail: "sarah@coffeecorner.com",
      shop: "B-205",
      amount: 320000,
      dueDate: "2024-02-15",
      paidDate: null,
      status: "overdue",
      type: "rent",
    },
    {
      id: 3,
      tenant: "Tech Services",
      tenantEmail: "mike@techservices.com",
      shop: "C-301",
      amount: 200000,
      dueDate: "2024-02-15",
      paidDate: "2024-02-14",
      status: "completed",
      type: "rent",
    },
    {
      id: 4,
      tenant: "Fashion Store",
      tenantEmail: "john@fashionstore.com",
      shop: "A-101",
      amount: 25000,
      dueDate: "2024-01-20",
      paidDate: "2024-01-18",
      status: "completed",
      type: "utilities",
    },
  ]

  const availableShops = [
    {
      id: "A-103",
      floor: "Ground Floor",
      size: "25 sqm",
      rent: 150000,
      status: "available",
      description: "Perfect for retail business with good foot traffic",
    },
    {
      id: "B-201",
      floor: "First Floor",
      size: "30 sqm",
      rent: 180000,
      status: "available",
      description: "Spacious shop ideal for clothing or electronics",
    },
    {
      id: "C-305",
      floor: "Second Floor",
      size: "20 sqm",
      rent: 120000,
      status: "available",
      description: "Compact space suitable for services or small retail",
    },
    {
      id: "A-107",
      floor: "Ground Floor",
      size: "35 sqm",
      rent: 200000,
      status: "available",
      description: "Large corner shop with excellent visibility",
    },
    {
      id: "B-208",
      floor: "First Floor",
      size: "28 sqm",
      rent: 170000,
      status: "available",
      description: "Well-lit space with modern amenities",
    },
  ]

  const tenantInvoices = [
    {
      id: "INV-001",
      shop: "A-101",
      amount: 150000,
      dueDate: "2024-02-15",
      status: "paid",
      description: "Monthly Rent - January 2024",
    },
    {
      id: "INV-002",
      shop: "A-101",
      amount: 150000,
      dueDate: "2024-03-15",
      status: "pending",
      description: "Monthly Rent - February 2024",
    },
    {
      id: "INV-003",
      shop: "A-101",
      amount: 25000,
      dueDate: "2024-01-20",
      status: "paid",
      description: "Utility Bill - January 2024",
    },
    {
      id: "INV-004",
      shop: "A-101",
      amount: 150000,
      dueDate: "2024-04-15",
      status: "overdue",
      description: "Monthly Rent - March 2024",
    },
  ]

  const filteredShops = availableShops.filter((shop) => {
    const matchesSearch =
      shop.id.toLowerCase().includes(searchTerm.toLowerCase()) ||
      shop.description.toLowerCase().includes(searchTerm.toLowerCase())
    const matchesFloor = selectedFloor === "all" || shop.floor === selectedFloor
    return matchesSearch && matchesFloor
  })

  const stats = [
    { title: "Total Users", value: "1,247", change: "+12%", icon: Users, color: "text-blue-600" },
    { title: "Shop Occupancy", value: "85%", change: "+5%", icon: Building, color: "text-green-600" },
    { title: "Applications", value: "23", change: "+8%", icon: FileText, color: "text-orange-600" },
    { title: "Monthly Revenue", value: "₦45,230,000", change: "+15%", icon: DollarSign, color: "text-purple-600" },
  ]

  const recentApplications = [
    { id: 1, shop: "A-101", applicant: "John Smith", status: "pending", date: "2024-01-15" },
    { id: 2, shop: "B-205", applicant: "Sarah Johnson", status: "approved", date: "2024-01-14" },
    { id: 3, shop: "C-301", applicant: "Mike Wilson", status: "rejected", date: "2024-01-13" },
    { id: 4, shop: "A-102", applicant: "Lisa Brown", status: "pending", date: "2024-01-12" },
  ]

  const recentPayments = [
    { id: 1, tenant: "Fashion Store", amount: 250000, shop: "A-101", date: "2024-01-15", status: "completed" },
    { id: 2, tenant: "Coffee Corner", amount: 320000, shop: "B-205", date: "2024-01-14", status: "completed" },
    { id: 3, tenant: "Tech Services", amount: 200000, shop: "C-301", date: "2024-01-13", status: "pending" },
  ]

  const renderAdminOverview = () => (
    <div className="space-y-6">
      {/* Key Metrics Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <Card>
          <CardContent className="p-6">
            <div className="text-center">
              <p className="text-2xl font-bold text-primary">₦4,850,000</p>
              <p className="text-sm text-gray-600">Monthly Revenue</p>
              <p className="text-xs text-green-600 mt-1">+15.2% from last month</p>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="text-center">
              <p className="text-2xl font-bold text-blue-600">90%</p>
              <p className="text-sm text-gray-600">Occupancy Rate</p>
              <p className="text-xs text-green-600 mt-1">+3.4% from last month</p>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="text-center">
              <p className="text-2xl font-bold text-orange-600">25</p>
              <p className="text-sm text-gray-600">New Applications</p>
              <p className="text-xs text-green-600 mt-1">+25% from last month</p>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="text-center">
              <p className="text-2xl font-bold text-green-600">₦58,200,000</p>
              <p className="text-sm text-gray-600">YTD Revenue</p>
              <p className="text-xs text-green-600 mt-1">+18.7% YoY</p>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Charts Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Revenue Trend Chart */}
        <Card>
          <CardHeader>
            <CardTitle>Revenue Trend</CardTitle>
            <CardDescription>Monthly revenue over the last 6 months</CardDescription>
          </CardHeader>
          <CardContent>
            <ChartContainer
              config={{
                revenue: {
                  label: "Revenue (₦)",
                  color: "hsl(var(--primary))",
                },
              }}
              className="h-[300px]"
            >
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={adminAnalyticsData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="month" />
                  <YAxis tickFormatter={(value) => `₦${(value / 1000000).toFixed(1)}M`} />
                  <ChartTooltip
                    content={<ChartTooltipContent />}
                    formatter={(value) => [`₦${Number(value).toLocaleString()}`, "Revenue"]}
                  />
                  <Line
                    type="monotone"
                    dataKey="revenue"
                    stroke="var(--color-revenue)"
                    strokeWidth={3}
                    dot={{ fill: "var(--color-revenue)", strokeWidth: 2, r: 4 }}
                  />
                </LineChart>
              </ResponsiveContainer>
            </ChartContainer>
          </CardContent>
        </Card>

        {/* Occupancy & Applications Chart */}
        <Card>
          <CardHeader>
            <CardTitle>Occupancy & Applications</CardTitle>
            <CardDescription>Occupancy rate and new applications trend</CardDescription>
          </CardHeader>
          <CardContent>
            <ChartContainer
              config={{
                occupancy: { label: "Occupancy (%)", color: "#22c55e" },
                applications: { label: "Applications", color: "#f59e0b" },
              }}
              className="h-[300px]"
            >
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={adminAnalyticsData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="month" />
                  <YAxis yAxisId="left" domain={[70, 95]} tickFormatter={(value) => `${value}%`} />
                  <YAxis yAxisId="right" orientation="right" />
                  <ChartTooltip content={<ChartTooltipContent />} />
                  <Bar yAxisId="left" dataKey="occupancy" fill="#22c55e" name="Occupancy %" radius={[4, 4, 0, 0]} />
                  <Bar
                    yAxisId="right"
                    dataKey="applications"
                    fill="#f59e0b"
                    name="Applications"
                    radius={[4, 4, 0, 0]}
                  />
                </BarChart>
              </ResponsiveContainer>
            </ChartContainer>
          </CardContent>
        </Card>

        {/* Payment Status Distribution */}
        <Card>
          <CardHeader>
            <CardTitle>Payment Status Overview</CardTitle>
            <CardDescription>Current payment status distribution</CardDescription>
          </CardHeader>
          <CardContent>
            <ChartContainer
              config={{
                completed: { label: "Completed", color: "#22c55e" },
                pending: { label: "Pending", color: "#eab308" },
                overdue: { label: "Overdue", color: "#ef4444" },
              }}
              className="h-[300px]"
            >
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={[
                      { name: "Completed", value: 3200000, color: "#22c55e" },
                      { name: "Pending", value: 850000, color: "#eab308" },
                      { name: "Overdue", value: 320000, color: "#ef4444" },
                    ]}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={100}
                    paddingAngle={5}
                    dataKey="value"
                  >
                    {[
                      { name: "Completed", value: 3200000, color: "#22c55e" },
                      { name: "Pending", value: 850000, color: "#eab308" },
                      { name: "Overdue", value: 320000, color: "#ef4444" },
                    ].map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Pie>
                  <ChartTooltip
                    content={<ChartTooltipContent />}
                    formatter={(value) => [`₦${Number(value).toLocaleString()}`, ""]}
                  />
                </PieChart>
              </ResponsiveContainer>
            </ChartContainer>
            <div className="flex justify-center gap-4 mt-4">
              {[
                { name: "Completed", color: "#22c55e" },
                { name: "Pending", color: "#eab308" },
                { name: "Overdue", color: "#ef4444" },
              ].map((item, index) => (
                <div key={index} className="flex items-center gap-2">
                  <div className="w-3 h-3 rounded-full" style={{ backgroundColor: item.color }}></div>
                  <span className="text-sm">{item.name}</span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Shop Categories Distribution */}
        <Card>
          <CardHeader>
            <CardTitle>Shop Categories</CardTitle>
            <CardDescription>Distribution of business types</CardDescription>
          </CardHeader>
          <CardContent>
            <ChartContainer
              config={{
                count: {
                  label: "Count",
                  color: "hsl(var(--primary))",
                },
              }}
              className="h-[300px]"
            >
              <ResponsiveContainer width="100%" height="100%">
                <BarChart
                  data={[
                    { category: "Retail", count: 25 },
                    { category: "Food & Beverage", count: 18 },
                    { category: "Services", count: 15 },
                    { category: "Electronics", count: 12 },
                    { category: "Clothing", count: 20 },
                  ]}
                >
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="category" />
                  <YAxis />
                  <ChartTooltip content={<ChartTooltipContent />} />
                  <Bar dataKey="count" fill="var(--color-count)" radius={[4, 4, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            </ChartContainer>
          </CardContent>
        </Card>
      </div>
    </div>
  )

  const renderTenantsManagement = () => (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h3 className="text-lg font-semibold">Tenant Management</h3>
        <div className="flex gap-2">
          <Input
            placeholder="Search tenants..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-64"
          />
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {tenantsList.map((tenant) => (
          <Card key={tenant.id} className="hover:shadow-md transition-shadow">
            <CardHeader>
              <div className="flex justify-between items-start">
                <div>
                  <CardTitle className="text-lg">{tenant.name}</CardTitle>
                  <CardDescription>{tenant.business}</CardDescription>
                </div>
                <Badge
                  className={
                    tenant.status === "active" ? "bg-green-100 text-green-800" : "bg-yellow-100 text-yellow-800"
                  }
                >
                  {tenant.status}
                </Badge>
              </div>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                <div className="flex items-center gap-2">
                  <Building className="w-4 h-4 text-gray-500" />
                  <span className="text-sm">Shop {tenant.shop}</span>
                </div>
                <div className="flex items-center gap-2">
                  <DollarSign className="w-4 h-4 text-gray-500" />
                  <span className="text-sm">₦{tenant.rent.toLocaleString()}/month</span>
                </div>
                <div className="flex items-center gap-2">
                  <User className="w-4 h-4 text-gray-500" />
                  <span className="text-sm">{tenant.email}</span>
                </div>
                <div className="flex items-center gap-2">
                  <Phone className="w-4 h-4 text-gray-500" />
                  <span className="text-sm">{tenant.phone}</span>
                </div>
                <div className="flex items-center gap-2">
                  <span className="text-sm">Payment Status:</span>
                  <Badge
                    className={
                      tenant.paymentStatus === "current" ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
                    }
                  >
                    {tenant.paymentStatus}
                  </Badge>
                </div>
                <div className="flex gap-2 pt-2">
                  <Button
                    size="sm"
                    variant="outline"
                    className="flex-1 bg-transparent"
                    onClick={() => setSelectedTenant(tenant)}
                  >
                    View Details
                  </Button>
                  <Button size="sm" className="flex-1" onClick={() => window.open(`tel:${tenant.phone}`, "_self")}>
                    Contact
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {selectedTenant && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-2xl font-bold">Tenant Details</h2>
              <Button variant="ghost" onClick={() => setSelectedTenant(null)}>
                <X className="w-4 h-4" />
              </Button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-4">
                <div>
                  <h3 className="font-semibold text-lg mb-2">Personal Information</h3>
                  <div className="space-y-2">
                    <p>
                      <strong>Name:</strong> {selectedTenant.name}
                    </p>
                    <p>
                      <strong>Email:</strong> {selectedTenant.email}
                    </p>
                    <p>
                      <strong>Phone:</strong> {selectedTenant.phone}
                    </p>
                    <p>
                      <strong>Join Date:</strong> {selectedTenant.joinDate}
                    </p>
                  </div>
                </div>

                <div>
                  <h3 className="font-semibold text-lg mb-2">Business Information</h3>
                  <div className="space-y-2">
                    <p>
                      <strong>Business Name:</strong> {selectedTenant.business}
                    </p>
                    <p>
                      <strong>Shop Number:</strong> {selectedTenant.shop}
                    </p>
                    <p>
                      <strong>Monthly Rent:</strong> ₦{selectedTenant.rent.toLocaleString()}
                    </p>
                  </div>
                </div>
              </div>

              <div className="space-y-4">
                <div>
                  <h3 className="font-semibold text-lg mb-2">Status Information</h3>
                  <div className="space-y-2">
                    <div className="flex items-center gap-2">
                      <span>
                        <strong>Tenant Status:</strong>
                      </span>
                      <Badge
                        className={
                          selectedTenant.status === "active"
                            ? "bg-green-100 text-green-800"
                            : "bg-yellow-100 text-yellow-800"
                        }
                      >
                        {selectedTenant.status}
                      </Badge>
                    </div>
                    <div className="flex items-center gap-2">
                      <span>
                        <strong>Payment Status:</strong>
                      </span>
                      <Badge
                        className={
                          selectedTenant.paymentStatus === "current"
                            ? "bg-green-100 text-green-800"
                            : "bg-red-100 text-red-800"
                        }
                      >
                        {selectedTenant.paymentStatus}
                      </Badge>
                    </div>
                  </div>
                </div>

                <div>
                  <h3 className="font-semibold text-lg mb-2">Actions</h3>
                  <div className="space-y-2">
                    <Button className="w-full" onClick={() => window.open(`mailto:${selectedTenant.email}`, "_self")}>
                      <Mail className="w-4 h-4 mr-2" />
                      Send Email
                    </Button>
                    <Button
                      className="w-full bg-transparent"
                      variant="outline"
                      onClick={() => window.open(`tel:${selectedTenant.phone}`, "_self")}
                    >
                      <Phone className="w-4 h-4 mr-2" />
                      Call Tenant
                    </Button>
                    {selectedTenant.status === "pending" && (
                      <div className="flex gap-2">
                        <Button
                          className="flex-1 bg-green-600 hover:bg-green-700"
                          onClick={() => {
                            setSelectedApplication(selectedTenant)
                            setApprovalAction("approve")
                            setShowApprovalDialog(true)
                          }}
                        >
                          <CheckCircle className="w-4 h-4 mr-1" />
                          Approve
                        </Button>
                        <Button
                          className="flex-1 bg-transparent"
                          variant="outline"
                          onClick={() => {
                            setSelectedApplication(selectedTenant)
                            setApprovalAction("reject")
                            setShowApprovalDialog(true)
                          }}
                        >
                          <XCircle className="w-4 h-4 mr-1" />
                          Reject
                        </Button>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )

  const renderApplicationsManagement = () => (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h3 className="text-lg font-semibold">Pending Applications</h3>
        <Badge className="bg-orange-100 text-orange-800">{pendingApplications.length} Pending</Badge>
      </div>

      <div className="space-y-4">
        {pendingApplications.map((application) => (
          <Card key={application.id}>
            <CardContent className="p-6">
              <div className="flex justify-between items-start">
                <div className="flex-1">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <h4 className="font-semibold text-lg">{application.applicant}</h4>
                      <p className="text-gray-600">{application.business}</p>
                      <p className="text-sm text-gray-500 flex items-center gap-1">
                        <Mail className="w-3 h-3" />
                        {application.email}
                      </p>
                    </div>
                    <div>
                      <p className="text-sm">
                        <strong>Preferred Shop:</strong> {application.preferredShop}
                      </p>
                      <p className="text-sm">
                        <strong>Business Type:</strong> {application.businessType}
                      </p>
                      <p className="text-sm">
                        <strong>Submitted:</strong> {application.submittedDate}
                      </p>
                    </div>
                  </div>
                  <div className="mt-4">
                    <p className="text-sm">
                      <strong>Experience:</strong> {application.experience}
                    </p>
                  </div>
                </div>
                <div className="flex gap-2 ml-4">
                  <Button
                    size="sm"
                    variant="outline"
                    className="bg-transparent"
                    onClick={() => setSelectedApplication(application)}
                  >
                    View Details
                  </Button>
                  <Button
                    size="sm"
                    className="bg-green-600 hover:bg-green-700"
                    onClick={() => {
                      setSelectedApplication(application)
                      setApprovalAction("approve")
                      setShowApprovalDialog(true)
                    }}
                  >
                    <CheckCircle className="w-4 h-4 mr-1" />
                    Approve
                  </Button>
                  <Button
                    size="sm"
                    variant="outline"
                    className="text-red-600 border-red-600 hover:bg-red-50 bg-transparent"
                    onClick={() => {
                      setSelectedApplication(application)
                      setApprovalAction("reject")
                      setShowApprovalDialog(true)
                    }}
                  >
                    <XCircle className="w-4 h-4 mr-1" />
                    Reject
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {selectedApplication && !showApprovalDialog && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-2xl font-bold">Application Details</h2>
              <Button variant="ghost" onClick={() => setSelectedApplication(null)}>
                <X className="w-4 h-4" />
              </Button>
            </div>

            <div className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h3 className="font-semibold text-lg mb-3">Applicant Information</h3>
                  <div className="space-y-2">
                    <p>
                      <strong>Name:</strong> {selectedApplication.applicant}
                    </p>
                    <p>
                      <strong>Email:</strong> {selectedApplication.email}
                    </p>
                    <p>
                      <strong>Business Name:</strong> {selectedApplication.business}
                    </p>
                    <p>
                      <strong>Business Type:</strong> {selectedApplication.businessType}
                    </p>
                  </div>
                </div>

                <div>
                  <h3 className="font-semibold text-lg mb-3">Application Details</h3>
                  <div className="space-y-2">
                    <p>
                      <strong>Preferred Shop:</strong> {selectedApplication.preferredShop}
                    </p>
                    <p>
                      <strong>Submitted Date:</strong> {selectedApplication.submittedDate}
                    </p>
                    <p>
                      <strong>Status:</strong>
                      <Badge className="ml-2 bg-orange-100 text-orange-800">{selectedApplication.status}</Badge>
                    </p>
                  </div>
                </div>
              </div>

              <div>
                <h3 className="font-semibold text-lg mb-3">Experience & Background</h3>
                <p className="text-gray-700">{selectedApplication.experience}</p>
              </div>

              <div className="flex gap-3 pt-4 border-t">
                <Button
                  className="flex-1 bg-green-600 hover:bg-green-700"
                  onClick={() => {
                    setApprovalAction("approve")
                    setShowApprovalDialog(true)
                  }}
                >
                  <CheckCircle className="w-4 h-4 mr-2" />
                  Approve Application
                </Button>
                <Button
                  className="flex-1 bg-transparent"
                  variant="outline"
                  onClick={() => {
                    setApprovalAction("reject")
                    setShowApprovalDialog(true)
                  }}
                >
                  <XCircle className="w-4 h-4 mr-2" />
                  Reject Application
                </Button>
                <Button variant="outline" onClick={() => window.open(`mailto:${selectedApplication.email}`, "_self")}>
                  <Mail className="w-4 h-4 mr-2" />
                  Contact
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}

      {showApprovalDialog && selectedApplication && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div className="text-center">
              <div
                className={`mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4 ${
                  approvalAction === "approve" ? "bg-green-100" : "bg-red-100"
                }`}
              >
                {approvalAction === "approve" ? (
                  <CheckCircle className="h-6 w-6 text-green-600" />
                ) : (
                  <XCircle className="h-6 w-6 text-red-600" />
                )}
              </div>
              <h3 className="text-lg font-medium mb-2">
                {approvalAction === "approve" ? "Approve Application" : "Reject Application"}
              </h3>
              <p className="text-gray-500 mb-6">
                Are you sure you want to {approvalAction} the application from{" "}
                <strong>{selectedApplication.applicant}</strong> for shop{" "}
                <strong>{selectedApplication.preferredShop}</strong>?
              </p>
              <div className="flex gap-3">
                <Button
                  variant="outline"
                  className="flex-1 bg-transparent"
                  onClick={() => {
                    setShowApprovalDialog(false)
                    setApprovalAction(null)
                  }}
                >
                  Cancel
                </Button>
                <Button
                  className={`flex-1 ${
                    approvalAction === "approve" ? "bg-green-600 hover:bg-green-700" : "bg-red-600 hover:bg-red-700"
                  }`}
                  onClick={() => {
                    // Handle approval/rejection logic here
                    console.log(`[v0] ${approvalAction}ing application for ${selectedApplication.applicant}`)
                    setShowApprovalDialog(false)
                    setSelectedApplication(null)
                    setApprovalAction(null)
                  }}
                >
                  {approvalAction === "approve" ? "Approve" : "Reject"}
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )

  const renderPaymentsManagement = () => (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h3 className="text-lg font-semibold">Payment Management</h3>
        <div className="flex gap-2">
          <Select>
            <SelectTrigger className="w-48">
              <SelectValue placeholder="Filter by status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Payments</SelectItem>
              <SelectItem value="completed">Completed</SelectItem>
              <SelectItem value="pending">Pending</SelectItem>
              <SelectItem value="overdue">Overdue</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <Card>
          <CardContent className="p-4">
            <div className="text-center">
              <p className="text-2xl font-bold text-green-600">
                ₦
                {allPayments
                  .filter((p) => p.status === "completed")
                  .reduce((sum, p) => sum + p.amount, 0)
                  .toLocaleString()}
              </p>
              <p className="text-sm text-gray-600">Completed Payments</p>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="text-center">
              <p className="text-2xl font-bold text-yellow-600">
                ₦
                {allPayments
                  .filter((p) => p.status === "pending")
                  .reduce((sum, p) => sum + p.amount, 0)
                  .toLocaleString()}
              </p>
              <p className="text-sm text-gray-600">Pending Payments</p>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="text-center">
              <p className="text-2xl font-bold text-red-600">
                ₦
                {allPayments
                  .filter((p) => p.status === "overdue")
                  .reduce((sum, p) => sum + p.amount, 0)
                  .toLocaleString()}
              </p>
              <p className="text-sm text-gray-600">Overdue Payments</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardContent className="p-6">
          <div className="space-y-4">
            {allPayments.map((payment) => (
              <div key={payment.id} className="flex items-center justify-between p-4 border rounded-lg">
                <div className="flex items-center gap-3">
                  {getStatusIcon(payment.status)}
                  <div>
                    <p className="font-medium">{payment.tenant}</p>
                    <p className="text-sm text-gray-600">
                      Shop {payment.shop} • {payment.type} • Due: {payment.dueDate}
                    </p>
                    {payment.paidDate && <p className="text-xs text-green-600">Paid: {payment.paidDate}</p>}
                  </div>
                </div>
                <div className="text-right flex items-center gap-4">
                  <div>
                    <p className="font-bold">₦{payment.amount.toLocaleString()}</p>
                    <Badge className={getStatusColor(payment.status)}>{payment.status}</Badge>
                  </div>
                  {payment.status === "overdue" && (
                    <Button size="sm" variant="outline" className="text-red-600 bg-transparent">
                      Send Reminder
                    </Button>
                  )}
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  )

  const renderApplicationForm = () => (
    <Card>
      <CardHeader>
        <CardTitle>Submit New Application</CardTitle>
        <CardDescription>Apply for a shop space in Mega School Plaza</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="applicant-name">Full Name</Label>
              <Input id="applicant-name" placeholder="Enter your full name" />
            </div>
            <div>
              <Label htmlFor="phone">Phone Number</Label>
              <Input id="phone" placeholder="Enter your phone number" />
            </div>
          </div>

          <div>
            <Label htmlFor="email">Email Address</Label>
            <Input id="email" type="email" placeholder="Enter your email address" />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="business-name-form">Business Name</Label>
              <Input id="business-name-form" placeholder="Enter your business name" />
            </div>
            <div>
              <Label htmlFor="business-type-form">Business Type</Label>
              <Select>
                <SelectTrigger>
                  <SelectValue placeholder="Select business type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="retail">Retail</SelectItem>
                  <SelectItem value="services">Services</SelectItem>
                  <SelectItem value="food">Food & Beverage</SelectItem>
                  <SelectItem value="electronics">Electronics</SelectItem>
                  <SelectItem value="clothing">Clothing</SelectItem>
                  <SelectItem value="other">Other</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div>
            <Label htmlFor="preferred-shop">Preferred Shop (Optional)</Label>
            <Select>
              <SelectTrigger>
                <SelectValue placeholder="Select preferred shop" />
              </SelectTrigger>
              <SelectContent>
                {availableShops.map((shop) => (
                  <SelectItem key={shop.id} value={shop.id}>
                    Shop {shop.id} - {shop.floor} (₦{shop.rent.toLocaleString()}/month)
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div>
            <Label htmlFor="business-description">Business Description</Label>
            <Textarea
              id="business-description"
              placeholder="Describe your business, products/services, and why you'd like to rent a shop in our plaza..."
              rows={4}
            />
          </div>

          <div>
            <Label htmlFor="experience">Business Experience</Label>
            <Textarea id="experience" placeholder="Tell us about your business experience and background..." rows={3} />
          </div>

          <Button className="w-full" size="lg">
            <Send className="w-4 h-4 mr-2" />
            Submit Application
          </Button>
        </div>
      </CardContent>
    </Card>
  )

  const renderTenantOverview = () => (
    <div className="space-y-6">
      {/* Tenant Overview Content */}
      <p>Tenant Overview Content Here</p>
    </div>
  )

  const renderTenantShops = () => (
    <div className="space-y-6">
      {/* Tenant Shops Content */}
      <p>Tenant Shops Content Here</p>
    </div>
  )

  const renderTenantInvoices = () => (
    <div className="space-y-6">
      {/* Tenant Invoices Content */}
      <p>Tenant Invoices Content Here</p>
    </div>
  )

  useEffect(() => {
    const userData = localStorage.getItem("user")
    if (!userData) {
      router.push("/auth/login")
      return
    }

    try {
      const parsedUser = JSON.parse(userData)
      setUser(parsedUser)
    } catch (error) {
      console.error("Error parsing user data:", error)
      router.push("/auth/login")
      return
    }

    setIsLoading(false)
  }, [router])

  const handleLogout = () => {
    localStorage.removeItem("user")
    router.push("/")
  }

  if (isLoading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
          <p className="text-gray-600">Loading dashboard...</p>
        </div>
      </div>
    )
  }

  if (!user) {
    return null // Will redirect to login
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 py-6">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-3xl font-bold text-gray-900">
                {user.role === "Tenant" ? "Tenant Portal" : "Admin Dashboard"}
              </h1>
              <p className="text-gray-600 mt-2">
                {user.role === "Tenant"
                  ? "Manage your shop and view account information"
                  : "Mega School Plaza Management Overview"}
              </p>
            </div>
            <div className="flex items-center gap-4">
              <div className="flex items-center gap-2 text-sm">
                <User className="w-4 h-4" />
                <div>
                  <p className="font-medium">{user.name}</p>
                  <p className="text-gray-500">{user.role}</p>
                </div>
              </div>
              <Button variant="outline" onClick={handleLogout} className="flex items-center gap-2 bg-transparent">
                <LogOut className="w-4 h-4" />
                Logout
              </Button>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-8 space-y-8">
        {user.role === "Tenant" && (
          <>
            {/* Tenant Navigation Tabs */}
            <div className="flex flex-wrap gap-2 mb-6">
              <Button
                variant={activeTab === "overview" ? "default" : "outline"}
                onClick={() => setActiveTab("overview")}
              >
                Overview
              </Button>
              <Button variant={activeTab === "shops" ? "default" : "outline"} onClick={() => setActiveTab("shops")}>
                Browse Shops
              </Button>
              <Button
                variant={activeTab === "invoices" ? "default" : "outline"}
                onClick={() => setActiveTab("invoices")}
              >
                My Invoices
              </Button>
              <Button variant={activeTab === "apply" ? "default" : "outline"} onClick={() => setActiveTab("apply")}>
                Apply for Shop
              </Button>
            </div>

            {activeTab === "overview" && renderTenantOverview()}
            {activeTab === "shops" && renderTenantShops()}
            {activeTab === "invoices" && renderTenantInvoices()}
            {activeTab === "apply" && renderApplicationForm()}
          </>
        )}

        {user.role !== "Tenant" && (
          <>
            {/* Admin Navigation Tabs */}
            <div className="flex flex-wrap gap-2 mb-6">
              <Button
                variant={activeTab === "overview" ? "default" : "outline"}
                onClick={() => setActiveTab("overview")}
              >
                Overview
              </Button>
              <Button variant={activeTab === "tenants" ? "default" : "outline"} onClick={() => setActiveTab("tenants")}>
                Tenants
              </Button>
              <Button
                variant={activeTab === "applications" ? "default" : "outline"}
                onClick={() => setActiveTab("applications")}
              >
                Applications
              </Button>
              <Button
                variant={activeTab === "payments" ? "default" : "outline"}
                onClick={() => setActiveTab("payments")}
              >
                Payments
              </Button>
              <Button variant={activeTab === "shops" ? "default" : "outline"} onClick={() => setActiveTab("shops")}>
                Shop Management
              </Button>
              <Button variant={activeTab === "reports" ? "default" : "outline"} onClick={() => setActiveTab("reports")}>
                Reports
              </Button>
            </div>

            {activeTab === "overview" && renderAdminOverview()}
            {activeTab === "tenants" && renderTenantsManagement()}
            {activeTab === "applications" && renderApplicationsManagement()}
            {activeTab === "payments" && renderPaymentsManagement()}
            {activeTab === "shops" && renderTenantShops()}
            {activeTab === "reports" && renderAdminOverview()}
          </>
        )}

        {(user.role !== "Tenant" || activeTab === "overview") && (
          <>
            {user.role !== "Tenant" && (
              <>
                {/* Stats Grid - Only for Admin/Manager */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                  {stats.map((stat, index) => (
                    <Card key={index}>
                      <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                          <div>
                            <p className="text-sm font-medium text-gray-600">{stat.title}</p>
                            <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                            <p className="text-sm text-green-600 flex items-center gap-1">
                              <TrendingUp className="w-3 h-3" />
                              {stat.change}
                            </p>
                          </div>
                          <stat.icon className={`w-8 h-8 ${stat.color}`} />
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </div>

                {/* Alerts */}
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                      <AlertTriangle className="w-5 h-5" />
                      System Alerts
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-3">
                      {[
                        { type: "warning", message: "3 invoices are overdue", action: "View Invoices" },
                        { type: "info", message: "2 leases expiring this month", action: "Review Leases" },
                        { type: "success", message: "Monthly revenue target achieved", action: "View Report" },
                      ].map((alert, index) => (
                        <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                          <span className="text-sm">{alert.message}</span>
                          <Button variant="outline" size="sm">
                            {alert.action}
                          </Button>
                        </div>
                      ))}
                    </div>
                  </CardContent>
                </Card>
              </>
            )}

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
              {/* Recent Applications */}
              <Card>
                <CardHeader>
                  <CardTitle>{user.role === "Tenant" ? "My Applications" : "Recent Applications"}</CardTitle>
                  <CardDescription>
                    {user.role === "Tenant" ? "Your shop rental applications" : "Latest shop rental applications"}
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {recentApplications.map((app) => (
                      <div key={app.id} className="flex items-center justify-between p-3 border rounded-lg">
                        <div className="flex items-center gap-3">
                          {getStatusIcon(app.status)}
                          <div>
                            <p className="font-medium">Shop {app.shop}</p>
                            <p className="text-sm text-gray-600">{app.applicant}</p>
                          </div>
                        </div>
                        <div className="text-right">
                          <Badge className={getStatusColor(app.status)}>{app.status}</Badge>
                          <p className="text-xs text-gray-500 mt-1">{app.date}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>

              {/* Recent Payments */}
              <Card>
                <CardHeader>
                  <CardTitle>{user.role === "Tenant" ? "My Payments" : "Recent Payments"}</CardTitle>
                  <CardDescription>
                    {user.role === "Tenant" ? "Your rent payment history" : "Latest rent payments received"}
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {recentPayments.map((payment) => (
                      <div key={payment.id} className="flex items-center justify-between p-3 border rounded-lg">
                        <div className="flex items-center gap-3">
                          {getStatusIcon(payment.status)}
                          <div>
                            <p className="font-medium">{payment.tenant}</p>
                            <p className="text-sm text-gray-600">Shop {payment.shop}</p>
                          </div>
                        </div>
                        <div className="text-right">
                          <p className="font-medium">₦{payment.amount.toLocaleString()}</p>
                          <p className="text-xs text-gray-500">{payment.date}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>

            {user.role !== "Tenant" && (
              <Card>
                <CardHeader>
                  <CardTitle>Shop Occupancy Overview</CardTitle>
                  <CardDescription>Current occupancy status by floor</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {[
                      { floor: "Ground Floor", occupied: 12, total: 15, percentage: 80 },
                      { floor: "First Floor", occupied: 18, total: 20, percentage: 90 },
                      { floor: "Second Floor", occupied: 14, total: 18, percentage: 78 },
                      { floor: "Third Floor", occupied: 8, total: 12, percentage: 67 },
                    ].map((floor, index) => (
                      <div key={index} className="space-y-2">
                        <div className="flex justify-between text-sm">
                          <span className="font-medium">{floor.floor}</span>
                          <span>
                            {floor.occupied}/{floor.total} shops ({floor.percentage}%)
                          </span>
                        </div>
                        <Progress value={floor.percentage} className="h-2" />
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            )}
          </>
        )}
      </div>
    </div>
  )
}
