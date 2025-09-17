-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 16, 2023 at 08:21 PM
-- Server version: 10.5.19-MariaDB-cll-lve
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u997099361_alias`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `account_number` varchar(191) NOT NULL,
  `account_details` text DEFAULT NULL,
  `account_type_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_detail_types`
--

CREATE TABLE `account_detail_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `account_subtype_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_detail_types`
--

INSERT INTO `account_detail_types` (`id`, `business_id`, `account_subtype_id`, `name`, `description`, `active`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 'Allowance for bad debts', 'Use Allowance for bad debts to estimate the part of Accounts Receivable that you think you might not collect. Use this only if you are keeping your books on the accrual basis.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(2, 0, 1, 'Assets available for sale', 'Use Assets available for sale to track assets that are available for sale that are not expected to be held for a long period of time.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(3, 0, 1, 'Development costs', 'Use Development costs to track amounts you deposit or set aside to arrange for financing, such as an SBA loan, or for deposits in anticipation of the purchase of property or other assets. When the deposit is refunded, or the purchase takes place, remove the amount from this account.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(4, 0, 1, 'Employee cash advances', 'Use Employee cash advances to track employee wages and salary you issue to an employee early, or other non-salary money given to employees. If you make a loan to an employee, use the Current asset account type called Loans to others, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(5, 0, 1, 'Inventory', 'Use Inventory to track the cost of goods your business purchases for resale. When the goods are sold, assign the sale to a Cost of sales account.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(6, 0, 1, 'Investments - Other', 'Use Investments - Other to track the value of investments not covered by other investment account types. Examples include publicly-traded shares, coins, or gold.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(7, 0, 1, 'Loans to officers', 'If you operate your business as a Corporation, use Loans to officers to track money loaned to officers of your business.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(8, 0, 1, 'Loans to others', 'Use Loans to others to track money your business loans to other people or businesses. This type of account is also referred to as Notes Receivable. For early salary payments to employees, use Employee cash advances, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(9, 0, 1, 'Loans to Shareholders', 'If you operate your business as a Corporation, use Loans to Shareholders to track money your business loans to its shareholders', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(10, 0, 1, 'Other current assets', 'Use Other current assets for current assets not covered by the other types. Current assets are likely to be converted to cash or used up in a year.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(11, 0, 1, 'Prepaid expenses', 'Use Prepaid expenses to track payments for expenses that you won\'t recognise until your next accounting period. When you recognise the expense, make a journal entry to transfer money from this account to the expense account.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(12, 0, 1, 'Retainage', 'Use Retainage if your customers regularly hold back a portion of a contract amount until you have completed a project. This type of account is often used in the construction industry, and only if you record income on an accrual basis.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(13, 0, 1, 'Undeposited funds', 'Use Undeposited funds for cash or cheques from sales that haven\'t been deposited yet. For petty cash, use Cash on hand, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(14, 0, 2, 'Accumulated amortisation of non-current assets', 'Use Accumulated amortisation of non-current assets to track how much you\'ve amortised an asset whose type is Non-Current Asset.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(15, 0, 2, 'Assets held for sale', 'Use Assets held for sale to track assets of a company that are available for sale that are not expected to be held for a long period of time.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(16, 0, 2, 'Deferred tax', 'Use Deferred tax for tax liabilities or assets that are to be used in future accounting periods.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(17, 0, 2, 'Goodwill', 'Use Goodwill only if you have acquired another company. It represents the intangible assets of the acquired company which gave it an advantage, such as favourable government relations, business name, outstanding credit ratings, location, superior management, customer lists, product quality, or good labour relations.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(18, 0, 2, 'Intangible assets', 'Use Intangible assets to track intangible assets that you plan to amortise. Examples include franchises, customer lists, copyrights, and patents.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(19, 0, 2, 'Lease buyout', 'Use Lease buyout to track lease payments to be applied toward the purchase of a leased asset. You don\'t track the leased asset itself until you purchase it.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(20, 0, 2, 'Licences', 'Use Licences to track non-professional licences for permission to engage in an activity, like selling alcohol or radio broadcasting. For fees associated with professional licences granted to individuals, use a Legal and professional fees expense account, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(21, 0, 2, 'Long-term investments', 'Use Long-term investments to track investments that have a maturity date of longer than one year.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(22, 0, 2, 'Organisational costs', 'Use Organisational costs to track costs incurred when forming a partnership or corporation. The costs include the legal and accounting costs necessary to organise the company, facilitate the filings of the legal documents, and other paperwork.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(23, 0, 2, 'Other non-current assets', 'Use Other non-current assets to track assets not covered by other types. Non-current assets are long-term assets that are expected to provide value for more than one year.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(24, 0, 2, 'Security deposits', 'Use Security deposits to track funds you\'ve paid to cover any potential costs incurred by damage, loss, or theft. The funds should be returned to you at the end of the contract. If you accept down payments, advance payments, security deposits, or other kinds of deposits, use an Other Current liabilities account with the detail type Other Current liabilities.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(25, 0, 3, 'Accumulated depletion', 'Use Accumulated depletion to track how much you deplete a natural resource.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(26, 0, 3, 'Accumulated depreciation on property, plant and equipment', 'Use Accumulated depreciation on property, plant and equipment to track how much you depreciate a fixed asset (a physical asset you do not expect to convert to cash during one year of normal operations).', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(27, 0, 3, 'Buildings', 'Use Buildings to track the cost of structures you own and use for your business. If you have a business in your home, consult your accountant. Use a Land account for the land portion of any real property you own, splitting the cost of the property between land and building in a logical method. A common method is to mimic the land-to-building ratio on the property tax statement.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(28, 0, 3, 'Depletable assets', 'Use Depletable assets to track natural resources, such as timberlands, oil wells, and mineral deposits.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(29, 0, 3, 'Furniture and fixtures', 'Use Furniture and fixtures to track any furniture and fixtures your business owns and uses, like a dental chair or sales booth.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(30, 0, 3, 'Land', 'Use Land to track assets that are not easily convertible to cash or not expected to become cash within the next year. For example, leasehold improvements.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(31, 0, 3, 'Leasehold improvements', 'Use Leasehold improvements to track improvements to a leased asset that increases the asset\'s value. For example, if you carpet a leased office space and are not reimbursed, that\'s a leasehold improvement.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(32, 0, 3, 'Machinery and equipment', 'Use Machinery and equipment to track computer hardware, as well as any other non-furniture fixtures or devices owned and used for your business. This includes equipment that you ride, like tractors and lawn mowers. Cars and lorries, however, should be tracked with Vehicle accounts, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(33, 0, 3, 'Other fixed asset', 'Use Other fixed asset for fixed assets that are not covered by other asset types. Fixed assets are physical property that you use in your business and that you do not expect to convert to cash or be used up during one year of normal operations.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(34, 0, 3, 'Vehicles', 'Use Vehicles to track the value of vehicles your business owns and uses for business. This includes off-road vehicles, air planes, helicopters, and boats. If you use a vehicle for both business and personal use, consult your accountant to see how you should track its value.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(35, 0, 4, 'Accounts Receivable(A/R)', 'Accounts receivable (also called A/R, Debtors, or Trade and other receivables) tracks money that customers owe you for products or services, and payments customers make. Ultimate Pos -Accounting Module  automatically creates one Accounts receivable account for you. Most businesses need only one. Each customer has a register, which functions like an Accounts receivable account for each customer.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(36, 0, 5, 'Bank', 'Use Bank accounts to track all your current activity, including debit card transactions. Each current account your company has at a bank or other financial institution should have its own Bank type account in Ultimate POS.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(37, 0, 5, 'Cash and Cash Equivalents', 'Use Cash and Cash Equivalents to track cash or assets that can be converted into cash immediately. For example, marketable securities and Treasury bills.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(38, 0, 5, 'Cash on hand', 'Use a Cash on hand account to track cash your company keeps for occasional expenses, also called petty cash. To track cash from sales that have not been deposited yet, use a pre-created account called Undeposited funds, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(39, 0, 5, 'Client trust accounts', 'Use Client trust accounts for money held by you for the benefit of someone else. For example, trust accounts are often used by attorneys to keep track of expense money their customers have given them. Often, to keep the amount in a trust account from looking like it\'s yours, the amount is offset in a \"contra\" liability account (a Current Liability).', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(40, 0, 5, 'Money market', 'Use Money market to track amounts in money market accounts. For investments, see Current Assets, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(41, 0, 5, 'Rents held in trust', 'Use Rents held in trust to track deposits and rent held on behalf of the property owners. Typically only property managers use this type of account.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(42, 0, 5, 'Savings', 'Use Savings accounts to track your savings and CD activity. Each savings account your company has at a bank or other financial institution should have its own Savings type account. For investments, see Current Assets, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(43, 0, 6, 'Accrued Liabilities', 'Use Accrued Liabilities to track expenses that a business has incurred but has not yet paid. For example, pensions for companies that contribute to a pension fund for their employees for their retirement.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(44, 0, 6, 'Client Trust accounts - liabilities', 'Use Client Trust accounts - liabilities to offset Client Trust accounts in assets. Amounts in these accounts are held by your business on behalf of others. They do not belong to your business, so should not appear to be yours on your balance sheet. This \"contra\" account takes care of that, as long as the two balances match.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(45, 0, 6, 'Current tax liability', 'Use Current tax liability to track the total amount of taxes collected but not yet paid to the government.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(46, 0, 6, 'Current portion of obligations under finance leases', 'Use Current portion of obligations under finance leases to track the value of lease payments due within the next 12 months.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(47, 0, 6, 'Dividends payable', 'Use Dividends payable to track dividends that are owed to shareholders but have not yet been paid.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(48, 0, 6, 'Income tax payable', 'Use Income tax payable to track monies that are due to pay the company\'s income tax liabilties.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(49, 0, 6, 'Insurance payable', 'Use Insurance payable to keep track of insurance amounts due. This account is most useful for businesses with monthly recurring insurance expenses.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(50, 0, 6, 'Line of credit', 'Use Line of credit to track the balance due on any lines of credit your business has. Each line of credit your business has should have its own Line of credit account.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(51, 0, 6, 'Loan payable', 'Use Loan payable to track loans your business owes which are payable within the next twelve months. For longer-term loans, use the Long-term liability called Notes payable, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(52, 0, 6, 'Other current liabilities', 'Use Other current liabilities to track monies owed by the company and due within one year.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(53, 0, 6, 'Payroll clearing', 'Use Payroll clearing to keep track of any non-tax amounts that you have deducted from employee paycheques or that you owe as a result of doing payroll. When you forward money to the appropriate suppliers, deduct the amount from the balance of this account. Do not use this account for tax amounts you have withheld or owe from paying employee wages. For those amounts, use the Payroll tax payable account instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(54, 0, 6, 'Payroll liabilities', 'Use Payroll liabilities to keep track of tax amounts that you owe to government agencies as a result of paying wages. This includes taxes withheld, health care premiums, employment insurance, government pensions, etc. When you forward the money to the government agency, deduct the amount from the balance of this account.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(55, 0, 6, 'Prepaid expenses payable', 'Use Prepaid expenses payable to track items such as property taxes that are due, but not yet deductible as an expense because the period they cover has not yet passed.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(56, 0, 6, 'Rents in trust - Liability', 'Use Rents in trust - liability to offset the Rents in trust amount in assets. Amounts in these accounts are held by your business on behalf of others. They do not belong to your business, so should not appear to be yours on your balance sheet. This \"contra\" account takes care of that, as long as the two balances match.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(57, 0, 6, 'Sales and service tax payable', 'Use Sales and service tax payable to track tax you have collected, but not yet remitted to your government tax agency. This includes value-added tax, goods and services tax, sales tax, and other consumption tax.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(58, 0, 7, 'Accrued holiday payable', 'Use Accrued holiday payable to track holiday earned but that has not been paid out to employees.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(59, 0, 7, 'Accrued Non-current liabilities', 'Use Accrued Non-current liabilities to track expenses that a business has incurred but has not yet paid. For example, pensions for companies that contribute to a pension fund for their employees for their retirement.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(60, 0, 7, 'Liabilities related to assets held for sale', 'Use Liabilities related to assets held for sale to track any liabilities that are directly related to assets being sold or written off.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(61, 0, 7, 'Long-term debt', 'Use Long-term debt to track loans and obligations with a maturity of longer than one year. For example, mortgages.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(62, 0, 7, 'Notes payable', 'Use Notes payable to track the amounts your business owes in long-term (over twelve months) loans. For shorter loans, use the Current liability account type called Loan payable, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(63, 0, 7, 'Other non-current liabilities', 'Use Other non-current liabilities to track liabilities due in more than twelve months that don\'t fit the other Non-Current liability account types.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(64, 0, 7, 'Shareholder notes payable', 'Use Shareholder notes payable to track long-term loan balances your business owes its shareholders.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(65, 0, 8, 'Accounts Payable (A/P)', 'Accounts payable (also called A/P, Trade and other payables, or Creditors) tracks amounts you owe to your suppliers. Ultimate POS automatically creates one Accounts Payable account for you. Most businesses need only one.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(66, 0, 9, 'Credit Card', 'Credit card accounts track the balance due on your business credit cards. Create one Credit card account for each credit card account your business uses.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(67, 0, 10, 'Accumulated Adjustment', 'Some corporations use this account to track adjustments to owner\'s equity that are not attributable to net income.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(68, 0, 10, 'Dividend disbursed', 'Use Dividend disbursed to track a payment given to its shareholders out of the company\'s retained earnings.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(69, 0, 10, 'Equity in earnings of subsidiaries', 'Use Equity in earnings of subsidiaries to track the original investment in shares of subsidiaries plus the share of earnings or losses from the operations of the subsidiary.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(70, 0, 10, 'Opening balance equity', 'Ultimate POS creates this account the first time you enter an opening balance for a balance sheet account. As you enter opening balances, Ultimate POS records the amounts in Opening balance equity. This ensures that you have a correct balance sheet for your company, even before you\'ve finished entering all your company\'s assets and liabilities.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(71, 0, 10, 'Ordinary shares', 'Corporations use Ordinary shares to track its ordinary shares in the hands of shareholders. The amount in this account should be the stated (or par) value of the stock.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(72, 0, 10, 'Other comprehensive income', 'Use Other comprehensive income to track the increases or decreases in income from various businesses that is not yet absorbed by the company.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(73, 0, 10, 'Owner\'s Equity', 'Corporations use Owner\'s equity to show the cumulative net income or loss of their business as of the beginning of the financial year.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(74, 0, 10, 'Paid-in capital or Surplus', 'Corporations use Paid-in capital to track amounts received from shareholders in exchange for shares that are over and above the shares\' stated (or par) value.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(75, 0, 10, 'Partner contributions', 'Partnerships use Partner contributions to track amounts partners contribute to the partnership during the year.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(76, 0, 10, 'Partner distributions', 'Partnerships use Partner distributions to track amounts distributed by the partnership to its partners during the year. Don\'t use this for regular payments to partners for interest or service. For regular payments, use a Guaranteed payments account (a Expense account in Payroll expenses), instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(77, 0, 10, 'Partner\'s Equity', 'Partnerships use Partner\'s equity to show the income remaining in the partnership for each partner as of the end of the prior year.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(78, 0, 10, 'Preferred Shares', 'Corporations use this account to track its preferred shares in the hands of shareholders. The amount in this account should be the stated (or par) value of the shares.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(79, 0, 10, 'Retained earnings', 'Ultimate POS adds this account when you create your company. Retained earnings tracks net income from previous financial years. Ultimate POS automatically transfers your profit (or loss) to Retained earnings at the end of each financial year.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(80, 0, 10, 'Share capital', 'Use Share capital to track the funds raised by issuing shares.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(81, 0, 10, 'Treasury shares', 'Corporations use Treasury shares to track amounts paid by the corporation to buy its own shares back from shareholders.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(82, 0, 11, 'Discounts/refunds given', 'Use Discounts/refunds given to track discounts you give to customers. This account typically has a negative balance so it offsets other income. For discounts from suppliers, use an expense account, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(83, 0, 11, 'Non-profit income', 'Use Non-profit income to track money coming in if you are a non-profit organisation.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(84, 0, 11, 'Other primary income', 'Use Other primary income to track income from normal business operations that doesn\'t fall into another Income type.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(85, 0, 11, 'Revenue - General', 'Use Revenue - General to track income from normal business operations that do not fit under any other category.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(86, 0, 11, 'Sales - retail', 'Use Sales - retail to track sales of goods/services that have a mark-up cost to consumers.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(87, 0, 11, 'Sales - wholesale', 'Use Sales - wholesale to track the sale of goods in quantity for resale purposes.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(88, 0, 11, 'Sales of Product Income', 'Use Sales of product income to track income from selling products. This can include all kinds of products, like crops and livestock, rental fees, performances, and food served.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(89, 0, 11, 'Service/fee income', 'Use Service/fee income to track income from services you perform or ordinary usage fees you charge. For fees customers pay you for late payments or other uncommon situations, use an Other Income account type called Other miscellaneous income, instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(90, 0, 11, 'Unapplied Cash Payment Income', 'Unapplied Cash Payment Income reports the Cash Basis income from customers payments you\'ve received but not applied to invoices or charges. In general, you would never use this directly on a purchase or sale transaction.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(91, 0, 12, 'Dividend income', 'Use Dividend income to track taxable dividends from investments.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(92, 0, 12, 'Interest earned', 'Use Interest earned to track interest from bank or savings accounts, investments, or interest payments to you on loans your business made.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(93, 0, 12, 'Loss on disposal of assets', 'Use Loss on disposal of assets to track losses realised on the disposal of assets.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(94, 0, 12, 'Other investment income', 'Use Other investment income to track other types of investment income that isn\'t from dividends or interest.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(95, 0, 12, 'Other miscellaneous income', 'Use Other miscellaneous income to track income that isn\'t from normal business operations, and doesn\'t fall into another Other Income type.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(96, 0, 12, 'Other operating income', 'Use Other operating income to track income from activities other than normal business operations. For example, Investment interest, foreign exchange gains, and rent income.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(97, 0, 12, 'Tax-exempt interest', 'Use Tax-exempt interest to record interest that isn\'t taxable, such as interest on money in tax-exempt retirement accounts, or interest from tax-exempt bonds.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(98, 0, 12, 'Unrealised loss on securities, net of tax', 'Use Unrealised loss on securities, net of tax to track losses on securities that have occurred but are yet been realised through a transaction. For example, shares whose value has fallen but that are still being held.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(99, 0, 13, 'Advertising/Promotional', 'Use Advertising/promotional to track money spent promoting your company. You may want different accounts of this type to track different promotional efforts (Yellow Pages, newspaper, radio, flyers, events, and so on). If the promotion effort is a meal, use Promotional meals instead.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(100, 0, 13, 'Amortisation Expense', 'Use Amortisation expense to track writing off of assets (such as intangible assets or investments) over the projected life of the assets.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(101, 0, 13, 'Auto', 'Use Auto to track costs associated with vehicles. You may want different accounts of this type to track petrol, repairs, and maintenance. If your business owns a car or lorry, you may want to track its value as a Fixed Asset, in addition to tracking its expenses.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(102, 0, 13, 'Bad debt', 'Use Bad debt to track debt you have written off.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(103, 0, 13, 'Bank charges', 'Use Bank charges for any fees you pay to financial institutions.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(104, 0, 13, 'Charitable contributions', 'Use Charitable contributions to track gifts to charity.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(105, 0, 13, 'Commissions and fees', 'Use Commissions and fees to track amounts paid to agents (such as brokers) in order for them to execute a trade.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(106, 0, 13, 'Cost of labour', 'Use Cost of labour to track the cost of paying employees to produce products or supply services. It includes all employment costs, including food and transportation, if applicable. This account is also available as a Cost of Sales (COS) account.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(107, 0, 13, 'Dues and subscriptions', 'Use Dues and subscriptions to track dues and subscriptions related to running your business. You may want different accounts of this type for professional dues, fees for licences that can\'t be transferred, magazines, newspapers, industry publications, or service subscriptions.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(108, 0, 13, 'Equipment rental', 'Use Equipment rental to track the cost of renting equipment to produce products or services. This account is also available as a Cost of Sales account. If you purchase equipment, use a Fixed asset account type called Machinery and equipment.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(109, 0, 13, 'Finance costs', 'Use Finance costs to track the costs of obtaining loans or credit. Examples of finance costs would be credit card fees, interest and mortgage costs.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(110, 0, 13, 'Income tax expense', 'Use Income tax expense to track income taxes that the company has paid to meet their tax obligations.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(111, 0, 13, 'Insurance', 'Use Insurance to track insurance payments. You may want different accounts of this type for different types of insurance (auto, general liability, and so on).', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(112, 0, 13, 'Interest paid', 'Use Interest paid for all types of interest you pay, including mortgage interest, finance charges on credit cards, or interest on loans.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(113, 0, 13, 'Legal and professional fees', 'Use Legal and professional fees to track money to pay to professionals to help you run your business. You may want different accounts of this type for payments to your accountant, attorney, or other consultants.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(114, 0, 13, 'Loss on discontinued operations, net of tax', 'Use Loss on discontinued operations, net of tax to track the loss realised when a part of the business ceases to operate or when a product line is discontinued.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(115, 0, 13, 'Management compensation', 'Use Management compensation to track remuneration paid to Management, Executives and non-Executives. For example, salary, fees, and benefits.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(116, 0, 13, 'Meals and entertainment', 'Use Meals and entertainment to track how much you spend on dining with your employees to promote morale. If you dine with a customer to promote your business, use a Promotional meals account, instead. Be sure to include who you ate with and the purpose of the meal when you enter the transaction.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(117, 0, 13, 'Office/general administrative expenses', 'Use Office/general administrative expenses to track all types of general or office-related expenses.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(118, 0, 13, 'Other miscellaneous service cost', 'Use Other miscellaneous service cost to track costs related to providing services that don\'t fall into another Expense type. This account is also available as a Cost of Sales (COS) account.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(119, 0, 13, 'Other selling expenses', 'Use Other selling expenses to track selling expenses incurred that do not fall under any other category.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(120, 0, 13, 'Payroll expenses', 'Use Payroll expenses to track payroll expenses. You may want different accounts of this type for things like: Compensation of officers, Guaranteed payments, Workers compensation, Salaries and wages, Payroll taxes', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(121, 0, 13, 'Rent or lease of buildings', 'Use Rent or lease of buildings to track rent payments you make.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(122, 0, 13, 'Repair and maintenance', 'Use Repair and maintenance to track any repairs and periodic maintenance fees. You may want different accounts of this type to track different types repair & maintenance expenses (auto, equipment, landscape, and so on).', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(123, 0, 13, 'Shipping and delivery expense', 'Use Shipping and delivery expense to track the cost of shipping and delivery of goods to customers.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(124, 0, 13, 'Supplies & materials', 'Use Supplies & materials to track the cost of raw goods and parts used or consumed when producing a product or providing a service. This account is also available as a Cost of Sales account.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(125, 0, 13, 'Taxes paid', 'Use Taxes paid to track taxes you pay. You may want different accounts of this type for payments to different tax agencies.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(126, 0, 13, 'Travel expenses - general and admin expenses', 'Use Travel expenses - general and admin expenses to track travelling costs incurred that are not directly related to the revenue-generating operation of the company. For example, flight tickets and hotel costs when performing job interviews.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(127, 0, 13, 'Travel expenses - selling expense', 'Use Travel expenses - selling expense to track travelling costs incurred that are directly related to the revenue-generating operation of the company. For example, flight tickets and hotel costs when selling products and services.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(128, 0, 13, 'Unapplied Cash Bill Payment Expense', 'Unapplied Cash Bill Payment Expense reports the Cash Basis expense from supplier payment cheques you\'ve sent but not yet applied to supplier bills. In general, you would never use this directly on a purchase or sale transaction.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(129, 0, 13, 'Utilities', 'Use Utilities to track utility payments. You may want different accounts of this type to track different types of utility payments (gas and electric, telephone, water, and so on).', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(130, 0, 14, 'Cost of labour - COS', 'Use Cost of labour - COS to track the cost of paying employees to produce products or supply services. It includes all employment costs, including food and transportation, if applicable.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(131, 0, 14, 'Equipment rental - COS', 'Use Equipment rental - COS to track the cost of renting equipment to produce products or services. If you purchase equipment, use a Fixed Asset account type called Machinery and equipment.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(132, 0, 14, 'Freight and delivery - COS', 'Use Freight and delivery - COS to track the cost of shipping/delivery of obtaining raw materials and producing finished goods for resale.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(133, 0, 14, 'Other costs of sales - COS', 'Use Other costs of sales - COS to track costs related to services or sales that you provide that don\'t fall into another Cost of Sales type.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(134, 0, 14, 'Supplies and materials - COS', 'Use Supplies and materials - COS to track the cost of raw goods and parts used or consumed when producing a product or providing a service.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(135, 0, 15, 'Amortisation', 'Use Amortisation to track amortisation of intangible assets. Amortisation is spreading the cost of an intangible asset over its useful life, like depreciation of fixed assets. You may want an amortisation account for each intangible asset you have.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(136, 0, 15, 'Depreciation', 'Use Depreciation to track how much you depreciate fixed assets. You may want a depreciation account for each fixed asset you have.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(137, 0, 15, 'Exchange Gain or Loss', 'Use Exchange Gain or Loss to track gains or losses that occur as a result of exchange rate fluctuations.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(138, 0, 15, 'Other expense', 'Use Other expense to track unusual or infrequent expenses that don\'t fall into another Other Expense type.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(139, 0, 15, 'Penalties and settlements', 'Use Penalties and settlements to track money you pay for violating laws or regulations, settling lawsuits, or other penalties.', 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `account_subtypes`
--

CREATE TABLE `account_subtypes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `account_type` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_subtypes`
--

INSERT INTO `account_subtypes` (`id`, `business_id`, `account_type`, `name`, `description`, `active`, `created_at`, `updated_at`) VALUES
(1, 0, 'asset', 'Current Assets', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(2, 0, 'asset', 'Non- Current Assets', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(3, 0, 'asset', 'Fixed Assets', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(4, 0, 'asset', 'Accounts Receivable(A/R)', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(5, 0, 'asset', 'Cash and Cash Equivalents (CCE)', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(6, 0, 'liability', 'Current Liabilities', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(7, 0, 'liability', 'Non- Current Liabilities', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(8, 0, 'liability', 'Accounts Payable (A/P)', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(9, 0, 'liability', 'Credit Card', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(10, 0, 'equity', 'Owner\'s Equity', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(11, 0, 'income', 'Income', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(12, 0, 'income', 'Other Income', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(13, 0, 'expense', 'Expense', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(14, 0, 'expense', 'Cost of Sales', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30'),
(15, 0, 'expense', 'Other Expense', NULL, 1, '2023-06-16 15:14:30', '2023-06-16 15:14:30');

-- --------------------------------------------------------

--
-- Table structure for table `account_transactions`
--

CREATE TABLE `account_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_id` int(11) NOT NULL,
  `type` enum('debit','credit') NOT NULL,
  `sub_type` enum('opening_balance','fund_transfer','deposit') DEFAULT NULL,
  `amount` decimal(22,4) NOT NULL,
  `reff_no` varchar(191) DEFAULT NULL,
  `operation_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `transaction_payment_id` int(11) DEFAULT NULL,
  `transfer_transaction_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `parent_account_type_id` int(11) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `log_name` varchar(191) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `subject_type` varchar(191) DEFAULT NULL,
  `event` varchar(191) DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL,
  `causer_id` int(11) DEFAULT NULL,
  `causer_type` varchar(191) DEFAULT NULL,
  `properties` text DEFAULT NULL,
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_id`, `subject_type`, `event`, `business_id`, `causer_id`, `causer_type`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'login', 1, 'App\\User', NULL, 1, 1, 'App\\User', '[]', NULL, '2023-06-16 15:03:47', '2023-06-16 15:03:47'),
(2, 'default', 'logout', 1, 'App\\User', NULL, 1, 1, 'App\\User', '[]', NULL, '2023-06-16 15:18:45', '2023-06-16 15:18:45');

-- --------------------------------------------------------

--
-- Table structure for table `aiassistance_history`
--

CREATE TABLE `aiassistance_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tool_type` varchar(191) NOT NULL,
  `input_data` text DEFAULT NULL,
  `tokens_used` int(11) NOT NULL DEFAULT 0,
  `output_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `asset_code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `quantity` decimal(22,4) NOT NULL,
  `model` varchar(191) DEFAULT NULL,
  `serial_no` varchar(191) DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_type` varchar(191) DEFAULT NULL,
  `unit_price` decimal(22,4) NOT NULL,
  `depreciation` decimal(22,4) DEFAULT NULL,
  `is_allocatable` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_maintenances`
--

CREATE TABLE `asset_maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `maitenance_id` varchar(191) DEFAULT NULL,
  `status` varchar(191) DEFAULT NULL,
  `priority` varchar(191) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `maintenance_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_transactions`
--

CREATE TABLE `asset_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `asset_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_type` varchar(191) NOT NULL,
  `ref_no` varchar(191) NOT NULL,
  `receiver` int(10) UNSIGNED DEFAULT NULL COMMENT 'id from users table, who receives asset',
  `quantity` decimal(22,4) NOT NULL,
  `transaction_datetime` datetime NOT NULL,
  `allocated_upto` date DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'id from asset_transactions table',
  `created_by` int(10) UNSIGNED NOT NULL COMMENT 'id from users table, who allocated asset',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_warranties`
--

CREATE TABLE `asset_warranties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asset_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `additional_cost` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `additional_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barcodes`
--

CREATE TABLE `barcodes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `width` double(22,4) DEFAULT NULL,
  `height` double(22,4) DEFAULT NULL,
  `paper_width` double(22,4) DEFAULT NULL,
  `paper_height` double(22,4) DEFAULT NULL,
  `top_margin` double(22,4) DEFAULT NULL,
  `left_margin` double(22,4) DEFAULT NULL,
  `row_distance` double(22,4) DEFAULT NULL,
  `col_distance` double(22,4) DEFAULT NULL,
  `stickers_in_one_row` int(11) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_continuous` tinyint(1) NOT NULL DEFAULT 0,
  `stickers_in_one_sheet` int(11) DEFAULT NULL,
  `business_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barcodes`
--

INSERT INTO `barcodes` (`id`, `name`, `description`, `width`, `height`, `paper_width`, `paper_height`, `top_margin`, `left_margin`, `row_distance`, `col_distance`, `stickers_in_one_row`, `is_default`, `is_continuous`, `stickers_in_one_sheet`, `business_id`, `created_at`, `updated_at`) VALUES
(1, '20 Labels per Sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 4\" x 1\", Labels per sheet: 20', 4.0000, 1.0000, 8.5000, 11.0000, 0.5000, 0.1250, 0.0000, 0.1875, 2, 0, 0, 20, NULL, '2017-12-18 06:13:44', '2017-12-18 06:13:44'),
(2, '30 Labels per sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 2.625\" x 1\", Labels per sheet: 30', 2.6250, 1.0000, 8.5000, 11.0000, 0.5000, 0.1880, 0.0000, 0.1250, 3, 0, 0, 30, NULL, '2017-12-18 06:04:39', '2017-12-18 06:10:40'),
(3, '32 Labels per sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 2\" x 1.25\", Labels per sheet: 32', 2.0000, 1.2500, 8.5000, 11.0000, 0.5000, 0.2500, 0.0000, 0.0000, 4, 0, 0, 32, NULL, '2017-12-18 05:55:40', '2017-12-18 05:55:40'),
(4, '40 Labels per sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 2\" x 1\", Labels per sheet: 40', 2.0000, 1.0000, 8.5000, 11.0000, 0.5000, 0.2500, 0.0000, 0.0000, 4, 0, 0, 40, NULL, '2017-12-18 05:58:40', '2017-12-18 05:58:40'),
(5, '50 Labels per Sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 1.5\" x 1\", Labels per sheet: 50', 1.5000, 1.0000, 8.5000, 11.0000, 0.5000, 0.5000, 0.0000, 0.0000, 5, 0, 0, 50, NULL, '2017-12-18 05:51:10', '2017-12-18 05:51:10'),
(6, 'Continuous Rolls - 31.75mm x 25.4mm', 'Label Size: 31.75mm x 25.4mm, Gap: 3.18mm', 1.2500, 1.0000, 1.2500, 0.0000, 0.1250, 0.0000, 0.1250, 0.0000, 1, 0, 1, NULL, NULL, '2017-12-18 05:51:10', '2017-12-18 05:51:10');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `waiter_id` int(10) UNSIGNED DEFAULT NULL,
  `table_id` int(10) UNSIGNED DEFAULT NULL,
  `correspondent_id` int(11) DEFAULT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `booking_start` datetime NOT NULL,
  `booking_end` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `booking_status` varchar(191) NOT NULL,
  `booking_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_capital`
--

CREATE TABLE `branch_capital` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `debit` decimal(11,2) DEFAULT NULL,
  `credit` decimal(11,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `use_for_repair` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'brands to be used on repair module',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `chart_of_account_id` bigint(20) UNSIGNED NOT NULL,
  `financial_year` varchar(191) NOT NULL,
  `month_1` double(8,2) NOT NULL,
  `month_2` double(8,2) NOT NULL,
  `month_3` double(8,2) NOT NULL,
  `month_4` double(8,2) NOT NULL,
  `month_5` double(8,2) NOT NULL,
  `month_6` double(8,2) NOT NULL,
  `month_7` double(8,2) NOT NULL,
  `month_8` double(8,2) NOT NULL,
  `month_9` double(8,2) NOT NULL,
  `month_10` double(8,2) NOT NULL,
  `month_11` double(8,2) NOT NULL,
  `month_12` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business`
--

CREATE TABLE `business` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `currency_id` int(10) UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `tax_number_1` varchar(100) DEFAULT NULL,
  `tax_label_1` varchar(10) DEFAULT NULL,
  `tax_number_2` varchar(100) DEFAULT NULL,
  `tax_label_2` varchar(10) DEFAULT NULL,
  `code_label_1` varchar(191) DEFAULT NULL,
  `code_1` varchar(191) DEFAULT NULL,
  `code_label_2` varchar(191) DEFAULT NULL,
  `code_2` varchar(191) DEFAULT NULL,
  `default_sales_tax` int(10) UNSIGNED DEFAULT NULL,
  `default_profit_percent` double(5,2) NOT NULL DEFAULT 0.00,
  `owner_id` int(10) UNSIGNED NOT NULL,
  `time_zone` varchar(191) NOT NULL DEFAULT 'Asia/Kolkata',
  `fy_start_month` tinyint(4) NOT NULL DEFAULT 1,
  `accounting_method` enum('fifo','lifo','avco') NOT NULL DEFAULT 'fifo',
  `default_sales_discount` decimal(5,2) DEFAULT NULL,
  `sell_price_tax` enum('includes','excludes') NOT NULL DEFAULT 'includes',
  `logo` varchar(191) DEFAULT NULL,
  `sku_prefix` varchar(191) DEFAULT NULL,
  `enable_product_expiry` tinyint(1) NOT NULL DEFAULT 0,
  `expiry_type` enum('add_expiry','add_manufacturing') NOT NULL DEFAULT 'add_expiry',
  `on_product_expiry` enum('keep_selling','stop_selling','auto_delete') NOT NULL DEFAULT 'keep_selling',
  `stop_selling_before` int(11) NOT NULL COMMENT 'Stop selling expied item n days before expiry',
  `enable_tooltip` tinyint(1) NOT NULL DEFAULT 1,
  `purchase_in_diff_currency` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Allow purchase to be in different currency then the business currency',
  `purchase_currency_id` int(10) UNSIGNED DEFAULT NULL,
  `p_exchange_rate` decimal(20,3) NOT NULL DEFAULT 1.000,
  `transaction_edit_days` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `stock_expiry_alert_days` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `keyboard_shortcuts` text DEFAULT NULL,
  `pos_settings` text DEFAULT NULL,
  `woocommerce_api_settings` text DEFAULT NULL,
  `woocommerce_skipped_orders` text DEFAULT NULL,
  `woocommerce_wh_oc_secret` varchar(191) DEFAULT NULL,
  `woocommerce_wh_ou_secret` varchar(191) DEFAULT NULL,
  `woocommerce_wh_od_secret` varchar(191) DEFAULT NULL,
  `woocommerce_wh_or_secret` varchar(191) DEFAULT NULL,
  `essentials_settings` longtext DEFAULT NULL,
  `manufacturing_settings` text DEFAULT NULL,
  `weighing_scale_setting` text NOT NULL COMMENT 'used to store the configuration of weighing scale',
  `enable_brand` tinyint(1) NOT NULL DEFAULT 1,
  `enable_category` tinyint(1) NOT NULL DEFAULT 1,
  `enable_sub_category` tinyint(1) NOT NULL DEFAULT 1,
  `enable_price_tax` tinyint(1) NOT NULL DEFAULT 1,
  `enable_purchase_status` tinyint(1) DEFAULT 1,
  `enable_lot_number` tinyint(1) NOT NULL DEFAULT 0,
  `default_unit` int(11) DEFAULT NULL,
  `enable_sub_units` tinyint(1) NOT NULL DEFAULT 0,
  `enable_racks` tinyint(1) NOT NULL DEFAULT 0,
  `enable_row` tinyint(1) NOT NULL DEFAULT 0,
  `enable_position` tinyint(1) NOT NULL DEFAULT 0,
  `enable_editing_product_from_purchase` tinyint(1) NOT NULL DEFAULT 1,
  `sales_cmsn_agnt` enum('logged_in_user','user','cmsn_agnt') DEFAULT NULL,
  `item_addition_method` tinyint(1) NOT NULL DEFAULT 1,
  `enable_inline_tax` tinyint(1) NOT NULL DEFAULT 1,
  `currency_symbol_placement` enum('before','after') NOT NULL DEFAULT 'before',
  `enabled_modules` text DEFAULT NULL,
  `date_format` varchar(191) NOT NULL DEFAULT 'm/d/Y',
  `time_format` enum('12','24') NOT NULL DEFAULT '24',
  `currency_precision` tinyint(4) NOT NULL DEFAULT 2,
  `quantity_precision` tinyint(4) NOT NULL DEFAULT 2,
  `ref_no_prefixes` text DEFAULT NULL,
  `theme_color` char(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `repair_settings` text DEFAULT NULL,
  `repair_jobsheet_settings` text DEFAULT NULL,
  `asset_settings` text DEFAULT NULL,
  `crm_settings` text DEFAULT NULL,
  `enable_rp` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `rp_name` varchar(191) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `amount_for_unit_rp` decimal(22,4) NOT NULL DEFAULT 1.0000 COMMENT 'rp is the short form of reward points',
  `min_order_total_for_rp` decimal(22,4) NOT NULL DEFAULT 1.0000 COMMENT 'rp is the short form of reward points',
  `max_rp_per_order` int(11) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `redeem_amount_per_unit_rp` decimal(22,4) NOT NULL DEFAULT 1.0000 COMMENT 'rp is the short form of reward points',
  `min_order_total_for_redeem` decimal(22,4) NOT NULL DEFAULT 1.0000 COMMENT 'rp is the short form of reward points',
  `min_redeem_point` int(11) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `max_redeem_point` int(11) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `rp_expiry_period` int(11) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `rp_expiry_type` enum('month','year') NOT NULL DEFAULT 'year' COMMENT 'rp is the short form of reward points',
  `email_settings` text DEFAULT NULL,
  `sms_settings` text DEFAULT NULL,
  `custom_labels` text DEFAULT NULL,
  `common_settings` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business`
--

INSERT INTO `business` (`id`, `name`, `currency_id`, `start_date`, `tax_number_1`, `tax_label_1`, `tax_number_2`, `tax_label_2`, `code_label_1`, `code_1`, `code_label_2`, `code_2`, `default_sales_tax`, `default_profit_percent`, `owner_id`, `time_zone`, `fy_start_month`, `accounting_method`, `default_sales_discount`, `sell_price_tax`, `logo`, `sku_prefix`, `enable_product_expiry`, `expiry_type`, `on_product_expiry`, `stop_selling_before`, `enable_tooltip`, `purchase_in_diff_currency`, `purchase_currency_id`, `p_exchange_rate`, `transaction_edit_days`, `stock_expiry_alert_days`, `keyboard_shortcuts`, `pos_settings`, `woocommerce_api_settings`, `woocommerce_skipped_orders`, `woocommerce_wh_oc_secret`, `woocommerce_wh_ou_secret`, `woocommerce_wh_od_secret`, `woocommerce_wh_or_secret`, `essentials_settings`, `manufacturing_settings`, `weighing_scale_setting`, `enable_brand`, `enable_category`, `enable_sub_category`, `enable_price_tax`, `enable_purchase_status`, `enable_lot_number`, `default_unit`, `enable_sub_units`, `enable_racks`, `enable_row`, `enable_position`, `enable_editing_product_from_purchase`, `sales_cmsn_agnt`, `item_addition_method`, `enable_inline_tax`, `currency_symbol_placement`, `enabled_modules`, `date_format`, `time_format`, `currency_precision`, `quantity_precision`, `ref_no_prefixes`, `theme_color`, `created_by`, `repair_settings`, `repair_jobsheet_settings`, `asset_settings`, `crm_settings`, `enable_rp`, `rp_name`, `amount_for_unit_rp`, `min_order_total_for_rp`, `max_rp_per_order`, `redeem_amount_per_unit_rp`, `min_order_total_for_redeem`, `min_redeem_point`, `max_redeem_point`, `rp_expiry_period`, `rp_expiry_type`, `email_settings`, `sms_settings`, `custom_labels`, `common_settings`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Nacional Code', 26, '2023-06-16', '19', 'iva', NULL, 'iva', NULL, NULL, NULL, NULL, NULL, 25.00, 1, 'America/Bogota', 1, 'fifo', NULL, 'includes', '1686945820_favicon.png', NULL, 0, 'add_expiry', 'keep_selling', 0, 1, 0, NULL, '1.000', 30, 30, '{\"pos\":{\"express_checkout\":\"shift+e\",\"pay_n_ckeckout\":\"shift+p\",\"draft\":\"shift+d\",\"cancel\":\"shift+c\",\"edit_discount\":\"shift+i\",\"edit_order_tax\":\"shift+t\",\"add_payment_row\":\"shift+r\",\"finalize_payment\":\"shift+f\",\"recent_product_quantity\":\"f2\",\"add_new_product\":\"f4\"}}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 1, 1, 1, 1, 1, 0, NULL, 0, 0, 0, 0, 1, NULL, 1, 0, 'before', '[\"purchases\",\"add_sale\",\"pos_sale\",\"stock_transfers\",\"stock_adjustment\",\"expenses\"]', 'm/d/Y', '24', 2, 2, '{\"purchase\":\"PO\",\"stock_transfer\":\"ST\",\"stock_adjustment\":\"SA\",\"sell_return\":\"CN\",\"expense\":\"EP\",\"contacts\":\"CO\",\"purchase_payment\":\"PP\",\"sell_payment\":\"SP\",\"business_location\":\"BL\"}', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '1.0000', '1.0000', NULL, '1.0000', '1.0000', NULL, NULL, NULL, 'year', NULL, NULL, NULL, NULL, 1, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `business_locations`
--

CREATE TABLE `business_locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` varchar(191) DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `landmark` text DEFAULT NULL,
  `country` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` char(7) NOT NULL,
  `invoice_scheme_id` int(10) UNSIGNED NOT NULL,
  `sale_invoice_scheme_id` int(11) DEFAULT NULL,
  `invoice_layout_id` int(10) UNSIGNED NOT NULL,
  `sale_invoice_layout_id` int(11) DEFAULT NULL,
  `selling_price_group_id` int(11) DEFAULT NULL,
  `print_receipt_on_invoice` tinyint(1) DEFAULT 1,
  `receipt_printer_type` enum('browser','printer') NOT NULL DEFAULT 'browser',
  `printer_id` int(11) DEFAULT NULL,
  `mobile` varchar(191) DEFAULT NULL,
  `alternate_number` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `featured_products` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `default_payment_accounts` text DEFAULT NULL,
  `custom_field1` varchar(191) DEFAULT NULL,
  `custom_field2` varchar(191) DEFAULT NULL,
  `custom_field3` varchar(191) DEFAULT NULL,
  `custom_field4` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_locations`
--

INSERT INTO `business_locations` (`id`, `business_id`, `location_id`, `name`, `landmark`, `country`, `state`, `city`, `zip_code`, `invoice_scheme_id`, `sale_invoice_scheme_id`, `invoice_layout_id`, `sale_invoice_layout_id`, `selling_price_group_id`, `print_receipt_on_invoice`, `receipt_printer_type`, `printer_id`, `mobile`, `alternate_number`, `email`, `website`, `featured_products`, `is_active`, `default_payment_accounts`, `custom_field1`, `custom_field2`, `custom_field3`, `custom_field4`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'BL0001', 'Nacional Code', 'falabella', 'colombia', 'antioquia', 'medellin', '540001', 1, NULL, 1, 1, NULL, 1, 'browser', NULL, '3504931577', '3504931577', '', '', NULL, 1, '{\"cash\":{\"is_enabled\":1,\"account\":null},\"card\":{\"is_enabled\":1,\"account\":null},\"cheque\":{\"is_enabled\":1,\"account\":null},\"bank_transfer\":{\"is_enabled\":1,\"account\":null},\"other\":{\"is_enabled\":1,\"account\":null},\"custom_pay_1\":{\"is_enabled\":1,\"account\":null},\"custom_pay_2\":{\"is_enabled\":1,\"account\":null},\"custom_pay_3\":{\"is_enabled\":1,\"account\":null},\"custom_pay_4\":{\"is_enabled\":1,\"account\":null},\"custom_pay_5\":{\"is_enabled\":1,\"account\":null},\"custom_pay_6\":{\"is_enabled\":1,\"account\":null},\"custom_pay_7\":{\"is_enabled\":1,\"account\":null}}', NULL, NULL, NULL, NULL, NULL, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `cash_denominations`
--

CREATE TABLE `cash_denominations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `amount` decimal(22,4) NOT NULL,
  `total_count` int(11) NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_registers`
--

CREATE TABLE `cash_registers` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('close','open') NOT NULL DEFAULT 'open',
  `closed_at` datetime DEFAULT NULL,
  `closing_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `total_card_slips` int(11) NOT NULL DEFAULT 0,
  `total_cheques` int(11) NOT NULL DEFAULT 0,
  `denominations` text DEFAULT NULL,
  `closing_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_register_transactions`
--

CREATE TABLE `cash_register_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `cash_register_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `pay_method` varchar(191) DEFAULT NULL,
  `type` enum('debit','credit') NOT NULL,
  `transaction_type` varchar(191) DEFAULT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `short_code` varchar(191) DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `woocommerce_cat_id` int(11) DEFAULT NULL,
  `category_type` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categorizables`
--

CREATE TABLE `categorizables` (
  `category_id` int(11) NOT NULL,
  `categorizable_type` varchar(191) NOT NULL,
  `categorizable_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chart_of_accounts`
--

CREATE TABLE `chart_of_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 133,
  `payment_type_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `account_subtype_id` bigint(20) UNSIGNED DEFAULT NULL,
  `detail_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` text DEFAULT NULL,
  `gl_code` int(11) DEFAULT NULL,
  `account_type` enum('asset','expense','equity','liability','income') NOT NULL DEFAULT 'asset',
  `opening_balance` decimal(11,2) NOT NULL DEFAULT 0.00,
  `reconcile_opening_balance` int(11) DEFAULT NULL,
  `allow_manual` tinyint(4) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

CREATE TABLE `cms_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL,
  `layout` varchar(191) DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `content` longtext DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `tags` varchar(191) DEFAULT NULL,
  `feature_image` varchar(191) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cms_pages`
--

INSERT INTO `cms_pages` (`id`, `type`, `layout`, `title`, `content`, `meta_description`, `tags`, `feature_image`, `priority`, `created_by`, `is_enabled`, `created_at`, `updated_at`) VALUES
(1, 'testimonial', NULL, 'David Parr', '<p class=\"testimonial-card-1__paragraph mb-3\">This software has become one of the backbones of our business. It helped us streamline the data of all our warehouses &amp; shops on one platform. I am not much of a techy guy but the admin panel also allows you to customize every aspect of the application.</p>\r\n          <p class=\"testimonial-card-1__paragraph mb-3\">All the reports help me to give an overview of different aspects of our business.</p>\r\n          <p class=\"testimonial-card-1__paragraph mb-3\">It is very powerful!</p>', NULL, NULL, NULL, 3, 9, 1, '2022-08-19 11:25:05', '2022-10-20 17:48:29'),
(2, 'testimonial', NULL, 'Tim Johnson', '<p>It is truly designed to help my business run more smoothly and efficiently. I can now track and manage inventory, customer information, and sales easily. The installation was quick and easy, and the interface is very user-friendly. I can now manage almost every business operation with one single software!</p>', NULL, NULL, NULL, NULL, 1, 1, '2022-09-10 16:07:17', '2022-10-20 17:23:39'),
(3, 'page', 'home', 'Automate your business management at very-Low cost', '<p>Best POS, Invoicing, Inventory &amp; Service management application for your growing business!</p>', NULL, NULL, NULL, 1, 0, 1, '2022-09-10 16:26:24', '2022-10-20 16:30:15'),
(4, 'page', 'contact', 'Contact Us', '<p>We\'re happy to receive your message. Ask us anything, we\'ll respond as soon as possible.</p>', NULL, NULL, NULL, NULL, 0, 1, '2022-09-10 16:26:24', '2022-09-16 12:04:20'),
(5, 'testimonial', NULL, 'Krishna Watt', '<p>We have used this for 9 months now. It has helped our business grow by leaps and bounds. The interface and its simplicity have made it easy for us to learn and get familiar with. The best thing about this software is that it is cloud-based and we can use it from our shop, warehouse, or mobile while traveling. I can also see its potential as we continue to grow. Highly Satisfied!</p>', NULL, NULL, NULL, NULL, 1, 1, '2022-09-16 12:07:25', '2022-10-20 17:27:31');

-- --------------------------------------------------------

--
-- Table structure for table `cms_page_metas`
--

CREATE TABLE `cms_page_metas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cms_page_id` bigint(20) UNSIGNED NOT NULL,
  `meta_key` varchar(191) NOT NULL,
  `meta_value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cms_page_metas`
--

INSERT INTO `cms_page_metas` (`id`, `cms_page_id`, `meta_key`, `meta_value`, `created_at`, `updated_at`) VALUES
(1, 3, 'industry', '{\"id\":\"1\",\"title\":\"We\'re Ready For Your Business!\",\"description\":\"<p>If you\'re in need of business management software, you\'ve come to the right place.&nbsp;<\\/p>\\r\\n<p>We have a clean, modern modular design that is sure to fit your business!<\\/p>\",\"content\":[{\"icon\":\"fas fa-store-alt\",\"title\":\"Departmental Store\",\"description\":\"Looking for a software solution that can help you manage and sell all of your essential items in one place? Look no further than our one-stop departmental store software. Whether you need to sell clothes, shoes, bags, or any other type of item, our software has you covered. Plus, our easy-to-use interface makes it simple to get started selling right away. So why wait? Get started today!\"},{\"icon\":\"fas fa-store\",\"title\":\"Retail & Wholesale\",\"description\":\"A complete suite of features to manage both retail & wholesales stores. Set multiple prices for different customer segments or different business locations.\"},{\"icon\":\"fas fa-notes-medical\",\"title\":\"Pharmacy\",\"description\":\"Our software is perfect for any pharmaceutical company. You can set product expiration dates and lot numbers, and sell in different units of measure. Stop selling expired & to-be-expired items to customers. Check details reports on stock expiry by lot numbers\"},{\"icon\":\"fas fa-prescription-bottle\",\"title\":\"Liquor\",\"description\":\"Easy to use for every liquor shop. Sell in ml of simple sell the bottle, you can easily manage them.\"},{\"icon\":\"fas fa-mobile-alt\",\"title\":\"Mobile & Electronics\",\"description\":\"Record inventory serial number, sell items with particular serial number,\"},{\"icon\":\"fas fa-wrench\",\"title\":\"Repair Shop\",\"description\":\"A complete suite of features to manage repair business, create job sheet, assign job sheet to technician, repair status, convert job sheet to invoices. Self link for customers to check repair progress\"},{\"icon\":\"fas fa-home\",\"title\":null,\"description\":null},{\"icon\":\"fas fa-home\",\"title\":null,\"description\":null}]}', NULL, '2022-10-20 16:30:15'),
(2, 3, 'feature', '{\"id\":\"2\",\"title\":\"Features to skyrocket \\ud83d\\ude80 your business growth\",\"description\":\"<p>The pace of business growth has never been faster.<\\/p>\\r\\n<p>That\'s why we&rsquo;ve designed our cloud-based POS software to help you get ahead.<\\/p>\\r\\n<p>The easiest, most reliable way to grow and manage your business is right at your fingertips with our cloud-based POS and inventory management system.<\\/p>\\r\\n<p>Right software can be a lifesaver for your business, and with it, you can revolutionize your business\'s growth<\\/p>\",\"content\":[{\"icon\":\"fas fa-cloud\",\"title\":\"Access Anywhere!\",\"description\":\"Check your business data from anywhere, anytime. \\r\\nSee live updates in realtime\"},{\"icon\":\"fas fa-check-double\",\"title\":\"No Installation\",\"description\":\"No software to install, No hardware dependency. Just open browser & start using.\"},{\"icon\":\"fas fa-sitemap\",\"title\":\"MultiBranches & Warehouse\",\"description\":\"Manage stock of multiple branches  in one place effortlessly & real-time.\"},{\"icon\":\"fas fa-user-lock\",\"title\":\"User access limit\",\"description\":\"Powerful user Role & Permission management to limit employees  access to business data\"},{\"icon\":\"fas fa-cubes\",\"title\":\"Stock Management\",\"description\":\"Manage stocks with from multiple locations, stock expiry, lot number, stock history & much more.\"},{\"icon\":\"fas fa-wrench\",\"title\":\"Service Management\",\"description\":\"Manage & easily invoice for services. Also dedicated repair service management features to schedule & deliver services on time\"},{\"icon\":\"fas fa-users\",\"title\":\"HR Management\",\"description\":\"With HR management easily track daily attendance, shift management, leaves, payroll, Holidays, Departments,  & Designations\"},{\"icon\":\"fas fa-handshake\",\"title\":\"Customer Management\",\"description\":\"CRM module help you to track leads lifecycle, followup with leads, sources, followup, launch campaigns, proposals and lot more\"},{\"icon\":\"fas fa-hand-peace\",\"title\":\"Simplified Interface\",\"description\":\"Simple to use interface to get work done in few clicks. Save your time & make it easy for staffs to use.\"},{\"icon\":\"fas fa-chart-line\",\"title\":\"Reports\",\"description\":\"Comes inbuilt with lot of report to help business owners analyse every revenue, inventory, payments & human resource.\"}]}', '2022-09-15 15:34:41', '2022-10-20 15:39:11');

-- --------------------------------------------------------

--
-- Table structure for table `cms_site_details`
--

CREATE TABLE `cms_site_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `site_key` varchar(191) NOT NULL,
  `site_value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cms_site_details`
--

INSERT INTO `cms_site_details` (`id`, `site_key`, `site_value`, `created_at`, `updated_at`) VALUES
(1, 'faqs', '[{\"question\":\"Why should we switch to cloud-based business management software compared to traditional desktop software?\",\"answer\":\"Cloud-based software provides you a low-cost, secure & mobile software solutions. Compared to traditional software that requires it to install on a computer - cloud-based can be accessed from anywhere without any hardware dependency.\"},{\"question\":\"How secure is my business data?\",\"answer\":\"All data are stored in our centralized database which is highly protected with multi-layers of privileges. We takes regular frequency interval automated backup to protect from any unwanted consequences.\"},{\"question\":\"Whom should I reach out to in case of any problems?\",\"answer\":\"Please contact our support, write us in the contact section or call to the number mentioned. You\'re assured to receive a response in a maximum of 24 hours.\"},{\"question\":\"Is this POS application Free?\",\"answer\":\"We do provide a free tier for very small business or business that is about to start. To take more advantage of this POS software we recommend that you upgrade to a better plan that unlocks more features.\"},{\"question\":\"Do you provide consultancy or implementation solutions for using this POS software?\",\"answer\":\"Yes, with a pro plan you can connect with one of our experts who will help you with implementing the solution for your business.\"},{\"question\":\"Are there any additional hardware required or subscription charges?\",\"answer\":\"This is cloud-based software. You\'ll only need a device with an internet connection & chrome browser. It runs within the browser. No additional hardware is required. But you can use some hardware like barcode scanners, and printers for your convenience to speed up work.\"}]', '2022-09-13 18:44:10', '2022-10-20 17:16:28'),
(2, 'statistics', '{\"tagline\":\"POS\'s Stats & Numbers\",\"description\":\"More & More businesses are adopting our cloud-based business management solution\",\"content\":[{\"stats\":\"75\",\"title\":\"REGISTERED BUSINESSES\"},{\"stats\":\"400+\",\"title\":\"DAILY USERS\"},{\"stats\":\"50K\",\"title\":\"INVOICES CREATED\"},{\"stats\":\"126+\",\"title\":\"ONLINE RESOURCES\"}]}', '2022-09-13 18:44:10', '2022-10-20 17:08:07'),
(3, 'google_analytics', 'null', '2022-09-14 11:14:40', '2022-09-16 11:49:09'),
(4, 'fb_pixel', 'null', '2022-09-14 11:14:40', '2022-09-16 11:49:09'),
(5, 'custom_js', 'null', '2022-09-14 11:14:40', '2022-09-16 11:49:09'),
(6, 'custom_css', 'null', '2022-09-14 11:14:40', '2022-09-16 11:49:09'),
(7, 'meta_tags', 'null', '2022-09-14 11:14:40', '2022-09-16 11:49:09'),
(8, 'chat_widget', 'null', '2022-09-14 11:27:14', '2022-09-16 11:49:09'),
(9, 'contact_us', '[{\"label\":\"Call\",\"num\":\"0000000000\"},{\"label\":\"Support\",\"num\":\"0000000000\"},{\"label\":\"Enquiry\",\"num\":\"0000000000\"}]', '2022-09-14 12:24:38', '2022-09-16 11:49:09'),
(10, 'mail_us', '[{\"label\":\"Contact\",\"email\":\"contact@example.com\"},{\"label\":\"Support\",\"email\":\"support@example.com\"}]', '2022-09-14 12:24:38', '2022-09-16 11:49:09'),
(11, 'follow_us', '{\"facebook\":\"https:\\/\\/www.facebook.com\\/test\",\"instagram\":\"https:\\/\\/www.instagram.com\\/test\",\"twitter\":\"https:\\/\\/twitter.com\\/test\",\"linkedin\":\"https:\\/\\/in.linkedin.com\\/company\\/test\",\"youtube\":\"https:\\/\\/www.youtube.com\\/c\\/test\"}', '2022-09-14 12:35:39', '2022-10-20 17:08:07'),
(12, 'logo', 'null', '2022-09-14 13:38:53', '2022-09-16 11:49:09'),
(13, 'notifiable_email', '\"notifyme@example.com\"', '2022-09-14 13:38:53', '2022-09-16 11:49:09');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL,
  `supplier_business_name` varchar(191) DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `prefix` varchar(191) DEFAULT NULL,
  `first_name` varchar(191) DEFAULT NULL,
  `middle_name` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `contact_id` varchar(191) DEFAULT NULL,
  `contact_status` varchar(191) NOT NULL DEFAULT 'active',
  `tax_number` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `address_line_1` text DEFAULT NULL,
  `address_line_2` text DEFAULT NULL,
  `zip_code` varchar(191) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mobile` varchar(191) NOT NULL,
  `landline` varchar(191) DEFAULT NULL,
  `alternate_number` varchar(191) DEFAULT NULL,
  `pay_term_number` int(11) DEFAULT NULL,
  `pay_term_type` enum('days','months') DEFAULT NULL,
  `credit_limit` decimal(22,4) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `converted_by` int(11) DEFAULT NULL,
  `converted_on` datetime DEFAULT NULL,
  `balance` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `total_rp` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `total_rp_used` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `total_rp_expired` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `shipping_address` text DEFAULT NULL,
  `shipping_custom_field_details` longtext DEFAULT NULL,
  `is_export` tinyint(1) NOT NULL DEFAULT 0,
  `export_custom_field_1` varchar(191) DEFAULT NULL,
  `export_custom_field_2` varchar(191) DEFAULT NULL,
  `export_custom_field_3` varchar(191) DEFAULT NULL,
  `export_custom_field_4` varchar(191) DEFAULT NULL,
  `export_custom_field_5` varchar(191) DEFAULT NULL,
  `export_custom_field_6` varchar(191) DEFAULT NULL,
  `position` varchar(191) DEFAULT NULL,
  `customer_group_id` int(11) DEFAULT NULL,
  `crm_source` varchar(191) DEFAULT NULL,
  `crm_life_stage` varchar(191) DEFAULT NULL,
  `custom_field1` varchar(191) DEFAULT NULL,
  `custom_field2` varchar(191) DEFAULT NULL,
  `custom_field3` varchar(191) DEFAULT NULL,
  `custom_field4` varchar(191) DEFAULT NULL,
  `custom_field5` varchar(191) DEFAULT NULL,
  `custom_field6` varchar(191) DEFAULT NULL,
  `custom_field7` varchar(191) DEFAULT NULL,
  `custom_field8` varchar(191) DEFAULT NULL,
  `custom_field9` varchar(191) DEFAULT NULL,
  `custom_field10` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `business_id`, `type`, `supplier_business_name`, `name`, `prefix`, `first_name`, `middle_name`, `last_name`, `email`, `contact_id`, `contact_status`, `tax_number`, `city`, `state`, `country`, `address_line_1`, `address_line_2`, `zip_code`, `dob`, `mobile`, `landline`, `alternate_number`, `pay_term_number`, `pay_term_type`, `credit_limit`, `created_by`, `converted_by`, `converted_on`, `balance`, `total_rp`, `total_rp_used`, `total_rp_expired`, `is_default`, `shipping_address`, `shipping_custom_field_details`, `is_export`, `export_custom_field_1`, `export_custom_field_2`, `export_custom_field_3`, `export_custom_field_4`, `export_custom_field_5`, `export_custom_field_6`, `position`, `customer_group_id`, `crm_source`, `crm_life_stage`, `custom_field1`, `custom_field2`, `custom_field3`, `custom_field4`, `custom_field5`, `custom_field6`, `custom_field7`, `custom_field8`, `custom_field9`, `custom_field10`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'customer', NULL, 'Walk-In Customer', NULL, NULL, NULL, NULL, NULL, 'CO0001', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '0.0000', 1, NULL, NULL, '0.0000', 0, 0, 0, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sortname` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `sortname`, `name`, `created_at`, `updated_at`) VALUES
(1, 'AF', 'Afghanistan', NULL, NULL),
(2, 'AL', 'Albania', NULL, NULL),
(3, 'DZ', 'Algeria', NULL, NULL),
(4, 'AS', 'American Samoa', NULL, NULL),
(5, 'AD', 'Andorra', NULL, NULL),
(6, 'AO', 'Angola', NULL, NULL),
(7, 'AI', 'Anguilla', NULL, NULL),
(8, 'AQ', 'Antarctica', NULL, NULL),
(9, 'AG', 'Antigua And Barbuda', NULL, NULL),
(10, 'AR', 'Argentina', NULL, NULL),
(11, 'AM', 'Armenia', NULL, NULL),
(12, 'AW', 'Aruba', NULL, NULL),
(13, 'AU', 'Australia', NULL, NULL),
(14, 'AT', 'Austria', NULL, NULL),
(15, 'AZ', 'Azerbaijan', NULL, NULL),
(16, 'BS', 'Bahamas The', NULL, NULL),
(17, 'BH', 'Bahrain', NULL, NULL),
(18, 'BD', 'Bangladesh', NULL, NULL),
(19, 'BB', 'Barbados', NULL, NULL),
(20, 'BY', 'Belarus', NULL, NULL),
(21, 'BE', 'Belgium', NULL, NULL),
(22, 'BZ', 'Belize', NULL, NULL),
(23, 'BJ', 'Benin', NULL, NULL),
(24, 'BM', 'Bermuda', NULL, NULL),
(25, 'BT', 'Bhutan', NULL, NULL),
(26, 'BO', 'Bolivia', NULL, NULL),
(27, 'BA', 'Bosnia and Herzegovina', NULL, NULL),
(28, 'BW', 'Botswana', NULL, NULL),
(29, 'BV', 'Bouvet Island', NULL, NULL),
(30, 'BR', 'Brazil', NULL, NULL),
(31, 'IO', 'British Indian Ocean Territory', NULL, NULL),
(32, 'BN', 'Brunei', NULL, NULL),
(33, 'BG', 'Bulgaria', NULL, NULL),
(34, 'BF', 'Burkina Faso', NULL, NULL),
(35, 'BI', 'Burundi', NULL, NULL),
(36, 'KH', 'Cambodia', NULL, NULL),
(37, 'CM', 'Cameroon', NULL, NULL),
(38, 'CA', 'Canada', NULL, NULL),
(39, 'CV', 'Cape Verde', NULL, NULL),
(40, 'KY', 'Cayman Islands', NULL, NULL),
(41, 'CF', 'Central African Republic', NULL, NULL),
(42, 'TD', 'Chad', NULL, NULL),
(43, 'CL', 'Chile', NULL, NULL),
(44, 'CN', 'China', NULL, NULL),
(45, 'CX', 'Christmas Island', NULL, NULL),
(46, 'CC', 'Cocos (Keeling) Islands', NULL, NULL),
(47, 'CO', 'Colombia', NULL, NULL),
(48, 'KM', 'Comoros', NULL, NULL),
(49, 'CG', 'Congo', NULL, NULL),
(50, 'CD', 'Congo The Democratic Republic Of The', NULL, NULL),
(51, 'CK', 'Cook Islands', NULL, NULL),
(52, 'CR', 'Costa Rica', NULL, NULL),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)', NULL, NULL),
(54, 'HR', 'Croatia (Hrvatska)', NULL, NULL),
(55, 'CU', 'Cuba', NULL, NULL),
(56, 'CY', 'Cyprus', NULL, NULL),
(57, 'CZ', 'Czech Republic', NULL, NULL),
(58, 'DK', 'Denmark', NULL, NULL),
(59, 'DJ', 'Djibouti', NULL, NULL),
(60, 'DM', 'Dominica', NULL, NULL),
(61, 'DO', 'Dominican Republic', NULL, NULL),
(62, 'TP', 'East Timor', NULL, NULL),
(63, 'EC', 'Ecuador', NULL, NULL),
(64, 'EG', 'Egypt', NULL, NULL),
(65, 'SV', 'El Salvador', NULL, NULL),
(66, 'GQ', 'Equatorial Guinea', NULL, NULL),
(67, 'ER', 'Eritrea', NULL, NULL),
(68, 'EE', 'Estonia', NULL, NULL),
(69, 'ET', 'Ethiopia', NULL, NULL),
(70, 'XA', 'External Territories of Australia', NULL, NULL),
(71, 'FK', 'Falkland Islands', NULL, NULL),
(72, 'FO', 'Faroe Islands', NULL, NULL),
(73, 'FJ', 'Fiji Islands', NULL, NULL),
(74, 'FI', 'Finland', NULL, NULL),
(75, 'FR', 'France', NULL, NULL),
(76, 'GF', 'French Guiana', NULL, NULL),
(77, 'PF', 'French Polynesia', NULL, NULL),
(78, 'TF', 'French Southern Territories', NULL, NULL),
(79, 'GA', 'Gabon', NULL, NULL),
(80, 'GM', 'Gambia The', NULL, NULL),
(81, 'GE', 'Georgia', NULL, NULL),
(82, 'DE', 'Germany', NULL, NULL),
(83, 'GH', 'Ghana', NULL, NULL),
(84, 'GI', 'Gibraltar', NULL, NULL),
(85, 'GR', 'Greece', NULL, NULL),
(86, 'GL', 'Greenland', NULL, NULL),
(87, 'GD', 'Grenada', NULL, NULL),
(88, 'GP', 'Guadeloupe', NULL, NULL),
(89, 'GU', 'Guam', NULL, NULL),
(90, 'GT', 'Guatemala', NULL, NULL),
(91, 'XU', 'Guernsey and Alderney', NULL, NULL),
(92, 'GN', 'Guinea', NULL, NULL),
(93, 'GW', 'Guinea-Bissau', NULL, NULL),
(94, 'GY', 'Guyana', NULL, NULL),
(95, 'HT', 'Haiti', NULL, NULL),
(96, 'HM', 'Heard and McDonald Islands', NULL, NULL),
(97, 'HN', 'Honduras', NULL, NULL),
(98, 'HK', 'Hong Kong S.A.R.', NULL, NULL),
(99, 'HU', 'Hungary', NULL, NULL),
(100, 'IS', 'Iceland', NULL, NULL),
(101, 'IN', 'India', NULL, NULL),
(102, 'ID', 'Indonesia', NULL, NULL),
(103, 'IR', 'Iran', NULL, NULL),
(104, 'IQ', 'Iraq', NULL, NULL),
(105, 'IE', 'Ireland', NULL, NULL),
(106, 'IL', 'Israel', NULL, NULL),
(107, 'IT', 'Italy', NULL, NULL),
(108, 'JM', 'Jamaica', NULL, NULL),
(109, 'JP', 'Japan', NULL, NULL),
(110, 'XJ', 'Jersey', NULL, NULL),
(111, 'JO', 'Jordan', NULL, NULL),
(112, 'KZ', 'Kazakhstan', NULL, NULL),
(113, 'KE', 'Kenya', NULL, NULL),
(114, 'KI', 'Kiribati', NULL, NULL),
(115, 'KP', 'Korea North', NULL, NULL),
(116, 'KR', 'Korea South', NULL, NULL),
(117, 'KW', 'Kuwait', NULL, NULL),
(118, 'KG', 'Kyrgyzstan', NULL, NULL),
(119, 'LA', 'Laos', NULL, NULL),
(120, 'LV', 'Latvia', NULL, NULL),
(121, 'LB', 'Lebanon', NULL, NULL),
(122, 'LS', 'Lesotho', NULL, NULL),
(123, 'LR', 'Liberia', NULL, NULL),
(124, 'LY', 'Libya', NULL, NULL),
(125, 'LI', 'Liechtenstein', NULL, NULL),
(126, 'LT', 'Lithuania', NULL, NULL),
(127, 'LU', 'Luxembourg', NULL, NULL),
(128, 'MO', 'Macau S.A.R.', NULL, NULL),
(129, 'MK', 'Macedonia', NULL, NULL),
(130, 'MG', 'Madagascar', NULL, NULL),
(131, 'MW', 'Malawi', NULL, NULL),
(132, 'MY', 'Malaysia', NULL, NULL),
(133, 'MV', 'Maldives', NULL, NULL),
(134, 'ML', 'Mali', NULL, NULL),
(135, 'MT', 'Malta', NULL, NULL),
(136, 'XM', 'Man (Isle of)', NULL, NULL),
(137, 'MH', 'Marshall Islands', NULL, NULL),
(138, 'MQ', 'Martinique', NULL, NULL),
(139, 'MR', 'Mauritania', NULL, NULL),
(140, 'MU', 'Mauritius', NULL, NULL),
(141, 'YT', 'Mayotte', NULL, NULL),
(142, 'MX', 'Mexico', NULL, NULL),
(143, 'FM', 'Micronesia', NULL, NULL),
(144, 'MD', 'Moldova', NULL, NULL),
(145, 'MC', 'Monaco', NULL, NULL),
(146, 'MN', 'Mongolia', NULL, NULL),
(147, 'MS', 'Montserrat', NULL, NULL),
(148, 'MA', 'Morocco', NULL, NULL),
(149, 'MZ', 'Mozambique', NULL, NULL),
(150, 'MM', 'Myanmar', NULL, NULL),
(151, 'NA', 'Namibia', NULL, NULL),
(152, 'NR', 'Nauru', NULL, NULL),
(153, 'NP', 'Nepal', NULL, NULL),
(154, 'AN', 'Netherlands Antilles', NULL, NULL),
(155, 'NL', 'Netherlands The', NULL, NULL),
(156, 'NC', 'New Caledonia', NULL, NULL),
(157, 'NZ', 'New Zealand', NULL, NULL),
(158, 'NI', 'Nicaragua', NULL, NULL),
(159, 'NE', 'Niger', NULL, NULL),
(160, 'NG', 'Nigeria', NULL, NULL),
(161, 'NU', 'Niue', NULL, NULL),
(162, 'NF', 'Norfolk Island', NULL, NULL),
(163, 'MP', 'Northern Mariana Islands', NULL, NULL),
(164, 'NO', 'Norway', NULL, NULL),
(165, 'OM', 'Oman', NULL, NULL),
(166, 'PK', 'Pakistan', NULL, NULL),
(167, 'PW', 'Palau', NULL, NULL),
(168, 'PS', 'Palestinian Territory Occupied', NULL, NULL),
(169, 'PA', 'Panama', NULL, NULL),
(170, 'PG', 'Papua new Guinea', NULL, NULL),
(171, 'PY', 'Paraguay', NULL, NULL),
(172, 'PE', 'Peru', NULL, NULL),
(173, 'PH', 'Philippines', NULL, NULL),
(174, 'PN', 'Pitcairn Island', NULL, NULL),
(175, 'PL', 'Poland', NULL, NULL),
(176, 'PT', 'Portugal', NULL, NULL),
(177, 'PR', 'Puerto Rico', NULL, NULL),
(178, 'QA', 'Qatar', NULL, NULL),
(179, 'RE', 'Reunion', NULL, NULL),
(180, 'RO', 'Romania', NULL, NULL),
(181, 'RU', 'Russia', NULL, NULL),
(182, 'RW', 'Rwanda', NULL, NULL),
(183, 'SH', 'Saint Helena', NULL, NULL),
(184, 'KN', 'Saint Kitts And Nevis', NULL, NULL),
(185, 'LC', 'Saint Lucia', NULL, NULL),
(186, 'PM', 'Saint Pierre and Miquelon', NULL, NULL),
(187, 'VC', 'Saint Vincent And The Grenadines', NULL, NULL),
(188, 'WS', 'Samoa', NULL, NULL),
(189, 'SM', 'San Marino', NULL, NULL),
(190, 'ST', 'Sao Tome and Principe', NULL, NULL),
(191, 'SA', 'Saudi Arabia', NULL, NULL),
(192, 'SN', 'Senegal', NULL, NULL),
(193, 'RS', 'Serbia', NULL, NULL),
(194, 'SC', 'Seychelles', NULL, NULL),
(195, 'SL', 'Sierra Leone', NULL, NULL),
(196, 'SG', 'Singapore', NULL, NULL),
(197, 'SK', 'Slovakia', NULL, NULL),
(198, 'SI', 'Slovenia', NULL, NULL),
(199, 'XG', 'Smaller Territories of the UK', NULL, NULL),
(200, 'SB', 'Solomon Islands', NULL, NULL),
(201, 'SO', 'Somalia', NULL, NULL),
(202, 'ZA', 'South Africa', NULL, NULL),
(203, 'GS', 'South Georgia', NULL, NULL),
(204, 'SS', 'South Sudan', NULL, NULL),
(205, 'ES', 'Spain', NULL, NULL),
(206, 'LK', 'Sri Lanka', NULL, NULL),
(207, 'SD', 'Sudan', NULL, NULL),
(208, 'SR', 'Suriname', NULL, NULL),
(209, 'SJ', 'Svalbard And Jan Mayen Islands', NULL, NULL),
(210, 'SZ', 'Swaziland', NULL, NULL),
(211, 'SE', 'Sweden', NULL, NULL),
(212, 'CH', 'Switzerland', NULL, NULL),
(213, 'SY', 'Syria', NULL, NULL),
(214, 'TW', 'Taiwan', NULL, NULL),
(215, 'TJ', 'Tajikistan', NULL, NULL),
(216, 'TZ', 'Tanzania', NULL, NULL),
(217, 'TH', 'Thailand', NULL, NULL),
(218, 'TG', 'Togo', NULL, NULL),
(219, 'TK', 'Tokelau', NULL, NULL),
(220, 'TO', 'Tonga', NULL, NULL),
(221, 'TT', 'Trinidad And Tobago', NULL, NULL),
(222, 'TN', 'Tunisia', NULL, NULL),
(223, 'TR', 'Turkey', NULL, NULL),
(224, 'TM', 'Turkmenistan', NULL, NULL),
(225, 'TC', 'Turks And Caicos Islands', NULL, NULL),
(226, 'TV', 'Tuvalu', NULL, NULL),
(227, 'UG', 'Uganda', NULL, NULL),
(228, 'UA', 'Ukraine', NULL, NULL),
(229, 'AE', 'United Arab Emirates', NULL, NULL),
(230, 'GB', 'United Kingdom', NULL, NULL),
(231, 'US', 'United States', NULL, NULL),
(232, 'UM', 'United States Minor Outlying Islands', NULL, NULL),
(233, 'UY', 'Uruguay', NULL, NULL),
(234, 'UZ', 'Uzbekistan', NULL, NULL),
(235, 'VU', 'Vanuatu', NULL, NULL),
(236, 'VA', 'Vatican City State (Holy See)', NULL, NULL),
(237, 'VE', 'Venezuela', NULL, NULL),
(238, 'VN', 'Vietnam', NULL, NULL),
(239, 'VG', 'Virgin Islands (British)', NULL, NULL),
(240, 'VI', 'Virgin Islands (US)', NULL, NULL),
(241, 'WF', 'Wallis And Futuna Islands', NULL, NULL),
(242, 'EH', 'Western Sahara', NULL, NULL),
(243, 'YE', 'Yemen', NULL, NULL),
(244, 'YU', 'Yugoslavia', NULL, NULL),
(245, 'ZM', 'Zambia', NULL, NULL),
(246, 'ZW', 'Zimbabwe', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `crm_call_logs`
--

CREATE TABLE `crm_call_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `call_type` varchar(191) DEFAULT NULL,
  `mobile_number` varchar(191) NOT NULL,
  `mobile_name` varchar(191) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_campaigns`
--

CREATE TABLE `crm_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `campaign_type` enum('sms','email') NOT NULL DEFAULT 'email',
  `subject` varchar(191) DEFAULT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `sent_on` datetime DEFAULT NULL,
  `contact_ids` text NOT NULL,
  `additional_info` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_contact_person_commissions`
--

CREATE TABLE `crm_contact_person_commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contact_person_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `commission_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_followup_invoices`
--

CREATE TABLE `crm_followup_invoices` (
  `follow_up_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_lead_users`
--

CREATE TABLE `crm_lead_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_marketplaces`
--

CREATE TABLE `crm_marketplaces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `marketplace` varchar(191) DEFAULT NULL,
  `site_key` varchar(191) DEFAULT NULL,
  `site_id` varchar(191) DEFAULT NULL,
  `assigned_users` text DEFAULT NULL,
  `crm_source_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_proposals`
--

CREATE TABLE `crm_proposals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `subject` text NOT NULL,
  `body` longtext NOT NULL,
  `cc` text DEFAULT NULL,
  `bcc` text DEFAULT NULL,
  `sent_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_proposal_templates`
--

CREATE TABLE `crm_proposal_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `subject` text NOT NULL,
  `body` longtext NOT NULL,
  `cc` text DEFAULT NULL,
  `bcc` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_schedules`
--

CREATE TABLE `crm_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `status` varchar(191) DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `schedule_type` enum('call','sms','meeting','email') NOT NULL DEFAULT 'email',
  `followup_category_id` int(11) DEFAULT NULL,
  `allow_notification` tinyint(1) NOT NULL DEFAULT 1,
  `notify_via` text DEFAULT NULL,
  `notify_before` int(11) DEFAULT NULL,
  `notify_type` enum('minute','hour','day') NOT NULL DEFAULT 'hour',
  `created_by` int(11) NOT NULL,
  `is_recursive` tinyint(1) NOT NULL DEFAULT 0,
  `recursion_days` int(11) DEFAULT NULL,
  `followup_additional_info` text DEFAULT NULL,
  `follow_up_by` varchar(191) DEFAULT NULL,
  `follow_up_by_value` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_schedule_logs`
--

CREATE TABLE `crm_schedule_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED NOT NULL,
  `log_type` enum('call','sms','meeting','email') NOT NULL DEFAULT 'email',
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `subject` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_schedule_users`
--

CREATE TABLE `crm_schedule_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `country` varchar(100) NOT NULL,
  `currency` varchar(100) NOT NULL,
  `code` varchar(25) NOT NULL,
  `symbol` varchar(25) NOT NULL,
  `thousand_separator` varchar(10) NOT NULL,
  `decimal_separator` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `country`, `currency`, `code`, `symbol`, `thousand_separator`, `decimal_separator`, `created_at`, `updated_at`) VALUES
(1, 'Albania', 'Leke', 'ALL', 'Lek', ',', '.', NULL, NULL),
(2, 'America', 'Dollars', 'USD', '$', ',', '.', NULL, NULL),
(3, 'Afghanistan', 'Afghanis', 'AF', '؋', ',', '.', NULL, NULL),
(4, 'Argentina', 'Pesos', 'ARS', '$', ',', '.', NULL, NULL),
(5, 'Aruba', 'Guilders', 'AWG', 'ƒ', ',', '.', NULL, NULL),
(6, 'Australia', 'Dollars', 'AUD', '$', ',', '.', NULL, NULL),
(7, 'Azerbaijan', 'New Manats', 'AZ', 'ман', ',', '.', NULL, NULL),
(8, 'Bahamas', 'Dollars', 'BSD', '$', ',', '.', NULL, NULL),
(9, 'Barbados', 'Dollars', 'BBD', '$', ',', '.', NULL, NULL),
(10, 'Belarus', 'Rubles', 'BYR', 'p.', ',', '.', NULL, NULL),
(11, 'Belgium', 'Euro', 'EUR', '€', ',', '.', NULL, NULL),
(12, 'Beliz', 'Dollars', 'BZD', 'BZ$', ',', '.', NULL, NULL),
(13, 'Bermuda', 'Dollars', 'BMD', '$', ',', '.', NULL, NULL),
(14, 'Bolivia', 'Bolivianos', 'BOB', '$b', ',', '.', NULL, NULL),
(15, 'Bosnia and Herzegovina', 'Convertible Marka', 'BAM', 'KM', ',', '.', NULL, NULL),
(16, 'Botswana', 'Pula\'s', 'BWP', 'P', ',', '.', NULL, NULL),
(17, 'Bulgaria', 'Leva', 'BG', 'лв', ',', '.', NULL, NULL),
(18, 'Brazil', 'Reais', 'BRL', 'R$', ',', '.', NULL, NULL),
(19, 'Britain [United Kingdom]', 'Pounds', 'GBP', '£', ',', '.', NULL, NULL),
(20, 'Brunei Darussalam', 'Dollars', 'BND', '$', ',', '.', NULL, NULL),
(21, 'Cambodia', 'Riels', 'KHR', '៛', ',', '.', NULL, NULL),
(22, 'Canada', 'Dollars', 'CAD', '$', ',', '.', NULL, NULL),
(23, 'Cayman Islands', 'Dollars', 'KYD', '$', ',', '.', NULL, NULL),
(24, 'Chile', 'Pesos', 'CLP', '$', ',', '.', NULL, NULL),
(25, 'China', 'Yuan Renminbi', 'CNY', '¥', ',', '.', NULL, NULL),
(26, 'Colombia', 'Pesos', 'COP', '$', ',', '.', NULL, NULL),
(27, 'Costa Rica', 'Colón', 'CRC', '₡', ',', '.', NULL, NULL),
(28, 'Croatia', 'Kuna', 'HRK', 'kn', ',', '.', NULL, NULL),
(29, 'Cuba', 'Pesos', 'CUP', '₱', ',', '.', NULL, NULL),
(30, 'Cyprus', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(31, 'Czech Republic', 'Koruny', 'CZK', 'Kč', ',', '.', NULL, NULL),
(32, 'Denmark', 'Kroner', 'DKK', 'kr', ',', '.', NULL, NULL),
(33, 'Dominican Republic', 'Pesos', 'DOP ', 'RD$', ',', '.', NULL, NULL),
(34, 'East Caribbean', 'Dollars', 'XCD', '$', ',', '.', NULL, NULL),
(35, 'Egypt', 'Pounds', 'EGP', '£', ',', '.', NULL, NULL),
(36, 'El Salvador', 'Colones', 'SVC', '$', ',', '.', NULL, NULL),
(37, 'England [United Kingdom]', 'Pounds', 'GBP', '£', ',', '.', NULL, NULL),
(38, 'Euro', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(39, 'Falkland Islands', 'Pounds', 'FKP', '£', ',', '.', NULL, NULL),
(40, 'Fiji', 'Dollars', 'FJD', '$', ',', '.', NULL, NULL),
(41, 'France', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(42, 'Ghana', 'Cedis', 'GHS', '¢', ',', '.', NULL, NULL),
(43, 'Gibraltar', 'Pounds', 'GIP', '£', ',', '.', NULL, NULL),
(44, 'Greece', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(45, 'Guatemala', 'Quetzales', 'GTQ', 'Q', ',', '.', NULL, NULL),
(46, 'Guernsey', 'Pounds', 'GGP', '£', ',', '.', NULL, NULL),
(47, 'Guyana', 'Dollars', 'GYD', '$', ',', '.', NULL, NULL),
(48, 'Holland [Netherlands]', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(49, 'Honduras', 'Lempiras', 'HNL', 'L', ',', '.', NULL, NULL),
(50, 'Hong Kong', 'Dollars', 'HKD', '$', ',', '.', NULL, NULL),
(51, 'Hungary', 'Forint', 'HUF', 'Ft', ',', '.', NULL, NULL),
(52, 'Iceland', 'Kronur', 'ISK', 'kr', ',', '.', NULL, NULL),
(53, 'India', 'Rupees', 'INR', '₹', ',', '.', NULL, NULL),
(54, 'Indonesia', 'Rupiahs', 'IDR', 'Rp', ',', '.', NULL, NULL),
(55, 'Iran', 'Rials', 'IRR', '﷼', ',', '.', NULL, NULL),
(56, 'Ireland', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(57, 'Isle of Man', 'Pounds', 'IMP', '£', ',', '.', NULL, NULL),
(58, 'Israel', 'New Shekels', 'ILS', '₪', ',', '.', NULL, NULL),
(59, 'Italy', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(60, 'Jamaica', 'Dollars', 'JMD', 'J$', ',', '.', NULL, NULL),
(61, 'Japan', 'Yen', 'JPY', '¥', ',', '.', NULL, NULL),
(62, 'Jersey', 'Pounds', 'JEP', '£', ',', '.', NULL, NULL),
(63, 'Kazakhstan', 'Tenge', 'KZT', 'лв', ',', '.', NULL, NULL),
(64, 'Korea [North]', 'Won', 'KPW', '₩', ',', '.', NULL, NULL),
(65, 'Korea [South]', 'Won', 'KRW', '₩', ',', '.', NULL, NULL),
(66, 'Kyrgyzstan', 'Soms', 'KGS', 'лв', ',', '.', NULL, NULL),
(67, 'Laos', 'Kips', 'LAK', '₭', ',', '.', NULL, NULL),
(68, 'Latvia', 'Lati', 'LVL', 'Ls', ',', '.', NULL, NULL),
(69, 'Lebanon', 'Pounds', 'LBP', '£', ',', '.', NULL, NULL),
(70, 'Liberia', 'Dollars', 'LRD', '$', ',', '.', NULL, NULL),
(71, 'Liechtenstein', 'Switzerland Francs', 'CHF', 'CHF', ',', '.', NULL, NULL),
(72, 'Lithuania', 'Litai', 'LTL', 'Lt', ',', '.', NULL, NULL),
(73, 'Luxembourg', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(74, 'Macedonia', 'Denars', 'MKD', 'ден', ',', '.', NULL, NULL),
(75, 'Malaysia', 'Ringgits', 'MYR', 'RM', ',', '.', NULL, NULL),
(76, 'Malta', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(77, 'Mauritius', 'Rupees', 'MUR', '₨', ',', '.', NULL, NULL),
(78, 'Mexico', 'Pesos', 'MXN', '$', ',', '.', NULL, NULL),
(79, 'Mongolia', 'Tugriks', 'MNT', '₮', ',', '.', NULL, NULL),
(80, 'Mozambique', 'Meticais', 'MZ', 'MT', ',', '.', NULL, NULL),
(81, 'Namibia', 'Dollars', 'NAD', '$', ',', '.', NULL, NULL),
(82, 'Nepal', 'Rupees', 'NPR', '₨', ',', '.', NULL, NULL),
(83, 'Netherlands Antilles', 'Guilders', 'ANG', 'ƒ', ',', '.', NULL, NULL),
(84, 'Netherlands', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(85, 'New Zealand', 'Dollars', 'NZD', '$', ',', '.', NULL, NULL),
(86, 'Nicaragua', 'Cordobas', 'NIO', 'C$', ',', '.', NULL, NULL),
(87, 'Nigeria', 'Nairas', 'NGN', '₦', ',', '.', NULL, NULL),
(88, 'North Korea', 'Won', 'KPW', '₩', ',', '.', NULL, NULL),
(89, 'Norway', 'Krone', 'NOK', 'kr', ',', '.', NULL, NULL),
(90, 'Oman', 'Rials', 'OMR', '﷼', ',', '.', NULL, NULL),
(91, 'Pakistan', 'Rupees', 'PKR', '₨', ',', '.', NULL, NULL),
(92, 'Panama', 'Balboa', 'PAB', 'B/.', ',', '.', NULL, NULL),
(93, 'Paraguay', 'Guarani', 'PYG', 'Gs', ',', '.', NULL, NULL),
(94, 'Peru', 'Nuevos Soles', 'PE', 'S/.', ',', '.', NULL, NULL),
(95, 'Philippines', 'Pesos', 'PHP', 'Php', ',', '.', NULL, NULL),
(96, 'Poland', 'Zlotych', 'PL', 'zł', ',', '.', NULL, NULL),
(97, 'Qatar', 'Rials', 'QAR', '﷼', ',', '.', NULL, NULL),
(98, 'Romania', 'New Lei', 'RO', 'lei', ',', '.', NULL, NULL),
(99, 'Russia', 'Rubles', 'RUB', 'руб', ',', '.', NULL, NULL),
(100, 'Saint Helena', 'Pounds', 'SHP', '£', ',', '.', NULL, NULL),
(101, 'Saudi Arabia', 'Riyals', 'SAR', '﷼', ',', '.', NULL, NULL),
(102, 'Serbia', 'Dinars', 'RSD', 'Дин.', ',', '.', NULL, NULL),
(103, 'Seychelles', 'Rupees', 'SCR', '₨', ',', '.', NULL, NULL),
(104, 'Singapore', 'Dollars', 'SGD', '$', ',', '.', NULL, NULL),
(105, 'Slovenia', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(106, 'Solomon Islands', 'Dollars', 'SBD', '$', ',', '.', NULL, NULL),
(107, 'Somalia', 'Shillings', 'SOS', 'S', ',', '.', NULL, NULL),
(108, 'South Africa', 'Rand', 'ZAR', 'R', ',', '.', NULL, NULL),
(109, 'South Korea', 'Won', 'KRW', '₩', ',', '.', NULL, NULL),
(110, 'Spain', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(111, 'Sri Lanka', 'Rupees', 'LKR', '₨', ',', '.', NULL, NULL),
(112, 'Sweden', 'Kronor', 'SEK', 'kr', ',', '.', NULL, NULL),
(113, 'Switzerland', 'Francs', 'CHF', 'CHF', ',', '.', NULL, NULL),
(114, 'Suriname', 'Dollars', 'SRD', '$', ',', '.', NULL, NULL),
(115, 'Syria', 'Pounds', 'SYP', '£', ',', '.', NULL, NULL),
(116, 'Taiwan', 'New Dollars', 'TWD', 'NT$', ',', '.', NULL, NULL),
(117, 'Thailand', 'Baht', 'THB', '฿', ',', '.', NULL, NULL),
(118, 'Trinidad and Tobago', 'Dollars', 'TTD', 'TT$', ',', '.', NULL, NULL),
(119, 'Turkey', 'Lira', 'TRY', 'TL', ',', '.', NULL, NULL),
(120, 'Turkey', 'Liras', 'TRL', '£', ',', '.', NULL, NULL),
(121, 'Tuvalu', 'Dollars', 'TVD', '$', ',', '.', NULL, NULL),
(122, 'Ukraine', 'Hryvnia', 'UAH', '₴', ',', '.', NULL, NULL),
(123, 'United Kingdom', 'Pounds', 'GBP', '£', ',', '.', NULL, NULL),
(124, 'United States of America', 'Dollars', 'USD', '$', ',', '.', NULL, NULL),
(125, 'Uruguay', 'Pesos', 'UYU', '$U', ',', '.', NULL, NULL),
(126, 'Uzbekistan', 'Sums', 'UZS', 'лв', ',', '.', NULL, NULL),
(127, 'Vatican City', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(128, 'Venezuela', 'Bolivares Fuertes', 'VEF', 'Bs', ',', '.', NULL, NULL),
(129, 'Vietnam', 'Dong', 'VND', '₫', ',', '.', NULL, NULL),
(130, 'Yemen', 'Rials', 'YER', '﷼', ',', '.', NULL, NULL),
(131, 'Zimbabwe', 'Zimbabwe Dollars', 'ZWD', 'Z$', ',', '.', NULL, NULL),
(132, 'Iraq', 'Iraqi dinar', 'IQD', 'د.ع', ',', '.', NULL, NULL),
(133, 'Kenya', 'Kenyan shilling', 'KES', 'KSh', ',', '.', NULL, NULL),
(134, 'Bangladesh', 'Taka', 'BDT', '৳', ',', '.', NULL, NULL),
(135, 'Algerie', 'Algerian dinar', 'DZD', 'د.ج', ' ', '.', NULL, NULL),
(136, 'United Arab Emirates', 'United Arab Emirates dirham', 'AED', 'د.إ', ',', '.', NULL, NULL),
(137, 'Uganda', 'Uganda shillings', 'UGX', 'USh', ',', '.', NULL, NULL),
(138, 'Tanzania', 'Tanzanian shilling', 'TZS', 'TSh', ',', '.', NULL, NULL),
(139, 'Angola', 'Kwanza', 'AOA', 'Kz', ',', '.', NULL, NULL),
(140, 'Kuwait', 'Kuwaiti dinar', 'KWD', 'KD', ',', '.', NULL, NULL),
(141, 'Bahrain', 'Bahraini dinar', 'BHD', 'BD', ',', '.', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_groups`
--

CREATE TABLE `customer_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `amount` double(5,2) NOT NULL,
  `price_calculation_type` varchar(191) DEFAULT 'percentage',
  `selling_price_group_id` int(11) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_configurations`
--

CREATE TABLE `dashboard_configurations` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `created_by` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `color` varchar(191) NOT NULL,
  `configuration` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `discount_type` varchar(191) DEFAULT NULL,
  `discount_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `spg` varchar(100) DEFAULT NULL COMMENT 'Applicable in specified selling price group only. Use of applicable_in_spg column is discontinued',
  `applicable_in_cg` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_variations`
--

CREATE TABLE `discount_variations` (
  `discount_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_and_notes`
--

CREATE TABLE `document_and_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `notable_id` int(11) NOT NULL,
  `notable_type` varchar(191) NOT NULL,
  `heading` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_allowances_and_deductions`
--

CREATE TABLE `essentials_allowances_and_deductions` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `description` varchar(191) NOT NULL,
  `type` enum('allowance','deduction') NOT NULL,
  `amount` decimal(22,4) NOT NULL,
  `amount_type` enum('fixed','percent') NOT NULL,
  `applicable_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_attendances`
--

CREATE TABLE `essentials_attendances` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `clock_in_time` datetime DEFAULT NULL,
  `clock_out_time` datetime DEFAULT NULL,
  `essentials_shift_id` int(11) DEFAULT NULL,
  `ip_address` varchar(191) DEFAULT NULL,
  `clock_in_note` text DEFAULT NULL,
  `clock_out_note` text DEFAULT NULL,
  `clock_in_location` text DEFAULT NULL,
  `clock_out_location` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_documents`
--

CREATE TABLE `essentials_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(191) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_document_shares`
--

CREATE TABLE `essentials_document_shares` (
  `id` int(10) UNSIGNED NOT NULL,
  `document_id` int(11) NOT NULL,
  `value_type` enum('user','role') NOT NULL,
  `value` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_holidays`
--

CREATE TABLE `essentials_holidays` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `business_id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_kb`
--

CREATE TABLE `essentials_kb` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `content` longtext DEFAULT NULL,
  `status` varchar(191) NOT NULL,
  `kb_type` varchar(191) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'id from essentials_kb table',
  `share_with` varchar(191) DEFAULT NULL COMMENT 'public, private, only_with',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_kb_users`
--

CREATE TABLE `essentials_kb_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kb_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_leaves`
--

CREATE TABLE `essentials_leaves` (
  `id` int(10) UNSIGNED NOT NULL,
  `essentials_leave_type_id` int(11) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `ref_no` varchar(191) DEFAULT NULL,
  `status` enum('pending','approved','cancelled') DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_leave_types`
--

CREATE TABLE `essentials_leave_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `leave_type` varchar(191) NOT NULL,
  `max_leave_count` int(11) DEFAULT NULL,
  `leave_count_interval` enum('month','year') DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_messages`
--

CREATE TABLE `essentials_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_payroll_groups`
--

CREATE TABLE `essentials_payroll_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL COMMENT 'payroll for work location',
  `name` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL,
  `payment_status` varchar(191) NOT NULL DEFAULT 'due',
  `gross_total` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_payroll_group_transactions`
--

CREATE TABLE `essentials_payroll_group_transactions` (
  `payroll_group_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_reminders`
--

CREATE TABLE `essentials_reminders` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `repeat` enum('one_time','every_day','every_week','every_month') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_shifts`
--

CREATE TABLE `essentials_shifts` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `type` enum('fixed_shift','flexible_shift') NOT NULL DEFAULT 'fixed_shift',
  `business_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_allowed_auto_clockout` tinyint(1) NOT NULL DEFAULT 0,
  `auto_clockout_time` time DEFAULT NULL,
  `holidays` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_todos_users`
--

CREATE TABLE `essentials_todos_users` (
  `todo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_todo_comments`
--

CREATE TABLE `essentials_todo_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `task_id` int(11) NOT NULL,
  `comment_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_to_dos`
--

CREATE TABLE `essentials_to_dos` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `task` text NOT NULL,
  `date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `task_id` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(191) DEFAULT NULL,
  `estimated_hours` varchar(191) DEFAULT NULL,
  `priority` varchar(191) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_user_allowance_and_deductions`
--

CREATE TABLE `essentials_user_allowance_and_deductions` (
  `user_id` int(11) NOT NULL,
  `allowance_deduction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_user_sales_targets`
--

CREATE TABLE `essentials_user_sales_targets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `target_start` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `target_end` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `commission_percent` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essentials_user_shifts`
--

CREATE TABLE `essentials_user_shifts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `essentials_shift_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(191) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_sub_taxes`
--

CREATE TABLE `group_sub_taxes` (
  `group_tax_id` int(10) UNSIGNED NOT NULL,
  `tax_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_layouts`
--

CREATE TABLE `invoice_layouts` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `header_text` text DEFAULT NULL,
  `invoice_no_prefix` varchar(191) DEFAULT NULL,
  `quotation_no_prefix` varchar(191) DEFAULT NULL,
  `invoice_heading` varchar(191) DEFAULT NULL,
  `sub_heading_line1` varchar(191) DEFAULT NULL,
  `sub_heading_line2` varchar(191) DEFAULT NULL,
  `sub_heading_line3` varchar(191) DEFAULT NULL,
  `sub_heading_line4` varchar(191) DEFAULT NULL,
  `sub_heading_line5` varchar(191) DEFAULT NULL,
  `invoice_heading_not_paid` varchar(191) DEFAULT NULL,
  `invoice_heading_paid` varchar(191) DEFAULT NULL,
  `quotation_heading` varchar(191) DEFAULT NULL,
  `sub_total_label` varchar(191) DEFAULT NULL,
  `discount_label` varchar(191) DEFAULT NULL,
  `tax_label` varchar(191) DEFAULT NULL,
  `total_label` varchar(191) DEFAULT NULL,
  `round_off_label` varchar(191) DEFAULT NULL,
  `total_due_label` varchar(191) DEFAULT NULL,
  `paid_label` varchar(191) DEFAULT NULL,
  `show_client_id` tinyint(1) NOT NULL DEFAULT 0,
  `client_id_label` varchar(191) DEFAULT NULL,
  `client_tax_label` varchar(191) DEFAULT NULL,
  `date_label` varchar(191) DEFAULT NULL,
  `date_time_format` varchar(191) DEFAULT NULL,
  `show_time` tinyint(1) NOT NULL DEFAULT 1,
  `show_brand` tinyint(1) NOT NULL DEFAULT 0,
  `show_sku` tinyint(1) NOT NULL DEFAULT 1,
  `show_cat_code` tinyint(1) NOT NULL DEFAULT 1,
  `show_expiry` tinyint(1) NOT NULL DEFAULT 0,
  `show_lot` tinyint(1) NOT NULL DEFAULT 0,
  `show_image` tinyint(1) NOT NULL DEFAULT 0,
  `show_sale_description` tinyint(1) NOT NULL DEFAULT 0,
  `sales_person_label` varchar(191) DEFAULT NULL,
  `show_sales_person` tinyint(1) NOT NULL DEFAULT 0,
  `table_product_label` varchar(191) DEFAULT NULL,
  `table_qty_label` varchar(191) DEFAULT NULL,
  `table_unit_price_label` varchar(191) DEFAULT NULL,
  `table_subtotal_label` varchar(191) DEFAULT NULL,
  `cat_code_label` varchar(191) DEFAULT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `show_logo` tinyint(1) NOT NULL DEFAULT 0,
  `show_business_name` tinyint(1) NOT NULL DEFAULT 0,
  `show_location_name` tinyint(1) NOT NULL DEFAULT 1,
  `show_landmark` tinyint(1) NOT NULL DEFAULT 1,
  `show_city` tinyint(1) NOT NULL DEFAULT 1,
  `show_state` tinyint(1) NOT NULL DEFAULT 1,
  `show_zip_code` tinyint(1) NOT NULL DEFAULT 1,
  `show_country` tinyint(1) NOT NULL DEFAULT 1,
  `show_mobile_number` tinyint(1) NOT NULL DEFAULT 1,
  `show_alternate_number` tinyint(1) NOT NULL DEFAULT 0,
  `show_email` tinyint(1) NOT NULL DEFAULT 0,
  `show_tax_1` tinyint(1) NOT NULL DEFAULT 1,
  `show_tax_2` tinyint(1) NOT NULL DEFAULT 0,
  `show_barcode` tinyint(1) NOT NULL DEFAULT 0,
  `show_payments` tinyint(1) NOT NULL DEFAULT 0,
  `show_customer` tinyint(1) NOT NULL DEFAULT 0,
  `customer_label` varchar(191) DEFAULT NULL,
  `commission_agent_label` varchar(191) DEFAULT NULL,
  `show_commission_agent` tinyint(1) NOT NULL DEFAULT 0,
  `show_reward_point` tinyint(1) NOT NULL DEFAULT 0,
  `highlight_color` varchar(10) DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `module_info` text DEFAULT NULL,
  `common_settings` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `business_id` int(10) UNSIGNED NOT NULL,
  `show_letter_head` tinyint(1) NOT NULL DEFAULT 0,
  `letter_head` varchar(191) DEFAULT NULL,
  `show_qr_code` tinyint(1) NOT NULL DEFAULT 0,
  `qr_code_fields` text DEFAULT NULL,
  `design` varchar(190) DEFAULT 'classic',
  `cn_heading` varchar(191) DEFAULT NULL COMMENT 'cn = credit note',
  `cn_no_label` varchar(191) DEFAULT NULL,
  `cn_amount_label` varchar(191) DEFAULT NULL,
  `table_tax_headings` text DEFAULT NULL,
  `show_previous_bal` tinyint(1) NOT NULL DEFAULT 0,
  `prev_bal_label` varchar(191) DEFAULT NULL,
  `change_return_label` varchar(191) DEFAULT NULL,
  `product_custom_fields` text DEFAULT NULL,
  `contact_custom_fields` text DEFAULT NULL,
  `location_custom_fields` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_layouts`
--

INSERT INTO `invoice_layouts` (`id`, `name`, `header_text`, `invoice_no_prefix`, `quotation_no_prefix`, `invoice_heading`, `sub_heading_line1`, `sub_heading_line2`, `sub_heading_line3`, `sub_heading_line4`, `sub_heading_line5`, `invoice_heading_not_paid`, `invoice_heading_paid`, `quotation_heading`, `sub_total_label`, `discount_label`, `tax_label`, `total_label`, `round_off_label`, `total_due_label`, `paid_label`, `show_client_id`, `client_id_label`, `client_tax_label`, `date_label`, `date_time_format`, `show_time`, `show_brand`, `show_sku`, `show_cat_code`, `show_expiry`, `show_lot`, `show_image`, `show_sale_description`, `sales_person_label`, `show_sales_person`, `table_product_label`, `table_qty_label`, `table_unit_price_label`, `table_subtotal_label`, `cat_code_label`, `logo`, `show_logo`, `show_business_name`, `show_location_name`, `show_landmark`, `show_city`, `show_state`, `show_zip_code`, `show_country`, `show_mobile_number`, `show_alternate_number`, `show_email`, `show_tax_1`, `show_tax_2`, `show_barcode`, `show_payments`, `show_customer`, `customer_label`, `commission_agent_label`, `show_commission_agent`, `show_reward_point`, `highlight_color`, `footer_text`, `module_info`, `common_settings`, `is_default`, `business_id`, `show_letter_head`, `letter_head`, `show_qr_code`, `qr_code_fields`, `design`, `cn_heading`, `cn_no_label`, `cn_amount_label`, `table_tax_headings`, `show_previous_bal`, `prev_bal_label`, `change_return_label`, `product_custom_fields`, `contact_custom_fields`, `location_custom_fields`, `created_at`, `updated_at`) VALUES
(1, 'Default', NULL, 'Invoice No.', NULL, 'Invoice', NULL, NULL, NULL, NULL, NULL, '', '', NULL, 'Subtotal', 'Discount', 'Tax', 'Total', NULL, 'Total Due', 'Total Paid', 0, NULL, NULL, 'Date', NULL, 1, 0, 1, 1, 0, 0, 0, 0, NULL, 0, 'Product', 'Quantity', 'Unit Price', 'Subtotal', NULL, NULL, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 0, 0, 1, 1, 'Customer', NULL, 0, 0, '#000000', '', NULL, NULL, 1, 1, 0, NULL, 0, NULL, 'classic', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_schemes`
--

CREATE TABLE `invoice_schemes` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `scheme_type` enum('blank','year') NOT NULL,
  `number_type` varchar(100) NOT NULL DEFAULT 'sequential',
  `prefix` varchar(191) DEFAULT NULL,
  `start_number` int(11) DEFAULT NULL,
  `invoice_count` int(11) NOT NULL DEFAULT 0,
  `total_digits` int(11) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_schemes`
--

INSERT INTO `invoice_schemes` (`id`, `business_id`, `name`, `scheme_type`, `number_type`, `prefix`, `start_number`, `invoice_count`, `total_digits`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'Default', 'blank', 'sequential', '', 1, 0, 4, 1, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_number` varchar(191) DEFAULT NULL,
  `payment_detail_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `currency_id` bigint(20) UNSIGNED DEFAULT NULL,
  `chart_of_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_type` varchar(191) DEFAULT NULL,
  `transaction_sub_type` varchar(191) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `month` varchar(191) DEFAULT NULL,
  `year` varchar(191) DEFAULT NULL,
  `reference` varchar(191) DEFAULT NULL,
  `contact_id` int(10) UNSIGNED DEFAULT NULL,
  `debit` decimal(65,4) DEFAULT NULL,
  `credit` decimal(65,4) DEFAULT NULL,
  `balance` decimal(65,4) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `reversed` tinyint(4) NOT NULL DEFAULT 0,
  `reversible` tinyint(4) NOT NULL DEFAULT 1,
  `manual_entry` tinyint(4) NOT NULL DEFAULT 0,
  `receipt` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `model_type` varchar(191) NOT NULL,
  `woocommerce_media_id` int(11) DEFAULT NULL,
  `model_media_type` varchar(191) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mfg_ingredient_groups`
--

CREATE TABLE `mfg_ingredient_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mfg_recipes`
--

CREATE TABLE `mfg_recipes` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `instructions` text DEFAULT NULL,
  `waste_percent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ingredients_cost` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `extra_cost` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `production_cost_type` varchar(191) DEFAULT 'percentage',
  `total_quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `final_price` decimal(22,4) NOT NULL,
  `sub_unit_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mfg_recipe_ingredients`
--

CREATE TABLE `mfg_recipe_ingredients` (
  `id` int(10) UNSIGNED NOT NULL,
  `mfg_recipe_id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(11) NOT NULL,
  `mfg_ingredient_group_id` int(11) DEFAULT NULL,
  `quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `waste_percent` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `sub_unit_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2017_07_05_071953_create_currencies_table', 1),
(9, '2017_07_05_073658_create_business_table', 1),
(10, '2017_07_22_075923_add_business_id_users_table', 1),
(11, '2017_07_23_113209_create_brands_table', 1),
(12, '2017_07_26_083429_create_permission_tables', 1),
(13, '2017_07_26_110000_create_tax_rates_table', 1),
(14, '2017_07_26_122313_create_units_table', 1),
(15, '2017_07_27_075706_create_contacts_table', 1),
(16, '2017_08_04_071038_create_categories_table', 1),
(17, '2017_08_08_115903_create_products_table', 1),
(18, '2017_08_09_061616_create_variation_templates_table', 1),
(19, '2017_08_09_061638_create_variation_value_templates_table', 1),
(20, '2017_08_10_061146_create_product_variations_table', 1),
(21, '2017_08_10_061216_create_variations_table', 1),
(22, '2017_08_19_054827_create_transactions_table', 1),
(23, '2017_08_31_073533_create_purchase_lines_table', 1),
(24, '2017_10_15_064638_create_transaction_payments_table', 1),
(25, '2017_10_31_065621_add_default_sales_tax_to_business_table', 1),
(26, '2017_11_20_051930_create_table_group_sub_taxes', 1),
(27, '2017_11_20_063603_create_transaction_sell_lines', 1),
(28, '2017_11_21_064540_create_barcodes_table', 1),
(29, '2017_11_23_181237_create_invoice_schemes_table', 1),
(30, '2017_12_25_122822_create_business_locations_table', 1),
(31, '2017_12_25_160253_add_location_id_to_transactions_table', 1),
(32, '2017_12_25_163227_create_variation_location_details_table', 1),
(33, '2018_01_04_115627_create_sessions_table', 1),
(34, '2018_01_05_112817_create_invoice_layouts_table', 1),
(35, '2018_01_06_112303_add_invoice_scheme_id_and_invoice_layout_id_to_business_locations', 1),
(36, '2018_01_08_104124_create_expense_categories_table', 1),
(37, '2018_01_08_123327_modify_transactions_table_for_expenses', 1),
(38, '2018_01_09_111005_modify_payment_status_in_transactions_table', 1),
(39, '2018_01_09_111109_add_paid_on_column_to_transaction_payments_table', 1),
(40, '2018_01_25_172439_add_printer_related_fields_to_business_locations_table', 1),
(41, '2018_01_27_184322_create_printers_table', 1),
(42, '2018_01_30_181442_create_cash_registers_table', 1),
(43, '2018_01_31_125836_create_cash_register_transactions_table', 1),
(44, '2018_02_07_173326_modify_business_table', 1),
(45, '2018_02_08_105425_add_enable_product_expiry_column_to_business_table', 1),
(46, '2018_02_08_111027_add_expiry_period_and_expiry_period_type_columns_to_products_table', 1),
(47, '2018_02_08_131118_add_mfg_date_and_exp_date_purchase_lines_table', 1),
(48, '2018_02_08_155348_add_exchange_rate_to_transactions_table', 1),
(49, '2018_02_09_124945_modify_transaction_payments_table_for_contact_payments', 1),
(50, '2018_02_12_113640_create_transaction_sell_lines_purchase_lines_table', 1),
(51, '2018_02_12_114605_add_quantity_sold_in_purchase_lines_table', 1),
(52, '2018_02_13_183323_alter_decimal_fields_size', 1),
(53, '2018_02_14_161928_add_transaction_edit_days_to_business_table', 1),
(54, '2018_02_15_161032_add_document_column_to_transactions_table', 1),
(55, '2018_02_17_124709_add_more_options_to_invoice_layouts', 1),
(56, '2018_02_19_111517_add_keyboard_shortcut_column_to_business_table', 1),
(57, '2018_02_19_121537_stock_adjustment_move_to_transaction_table', 1),
(58, '2018_02_20_165505_add_is_direct_sale_column_to_transactions_table', 1),
(59, '2018_02_21_105329_create_system_table', 1),
(60, '2018_02_23_100549_version_1_2', 1),
(61, '2018_02_23_125648_add_enable_editing_sp_from_purchase_column_to_business_table', 1),
(62, '2018_02_26_103612_add_sales_commission_agent_column_to_business_table', 1),
(63, '2018_02_26_130519_modify_users_table_for_sales_cmmsn_agnt', 1),
(64, '2018_02_26_134500_add_commission_agent_to_transactions_table', 1),
(65, '2018_02_27_121422_add_item_addition_method_to_business_table', 1),
(66, '2018_02_27_170232_modify_transactions_table_for_stock_transfer', 1),
(67, '2018_03_05_153510_add_enable_inline_tax_column_to_business_table', 1),
(68, '2018_03_06_210206_modify_product_barcode_types', 1),
(69, '2018_03_13_181541_add_expiry_type_to_business_table', 1),
(70, '2018_03_16_113446_product_expiry_setting_for_business', 1),
(71, '2018_03_19_113601_add_business_settings_options', 1),
(72, '2018_03_26_125334_add_pos_settings_to_business_table', 1),
(73, '2018_03_26_165350_create_customer_groups_table', 1),
(74, '2018_03_27_122720_customer_group_related_changes_in_tables', 1),
(75, '2018_03_29_110138_change_tax_field_to_nullable_in_business_table', 1),
(76, '2018_03_29_115502_add_changes_for_sr_number_in_products_and_sale_lines_table', 1),
(77, '2018_03_29_134340_add_inline_discount_fields_in_purchase_lines', 1),
(78, '2018_03_31_140921_update_transactions_table_exchange_rate', 1),
(79, '2018_04_03_103037_add_contact_id_to_contacts_table', 1),
(80, '2018_04_03_122709_add_changes_to_invoice_layouts_table', 1),
(81, '2018_04_09_135320_change_exchage_rate_size_in_business_table', 1),
(82, '2018_04_17_123122_add_lot_number_to_business', 1),
(83, '2018_04_17_160845_add_product_racks_table', 1),
(84, '2018_04_20_182015_create_res_tables_table', 1),
(85, '2018_04_24_105246_restaurant_fields_in_transaction_table', 1),
(86, '2018_04_24_114149_add_enabled_modules_business_table', 1),
(87, '2018_04_24_133704_add_modules_fields_in_invoice_layout_table', 1),
(88, '2018_04_27_132653_quotation_related_change', 1),
(89, '2018_05_02_104439_add_date_format_and_time_format_to_business', 1),
(90, '2018_05_02_111939_add_sell_return_to_transaction_payments', 1),
(91, '2018_05_14_114027_add_rows_positions_for_products', 1),
(92, '2018_05_14_125223_add_weight_to_products_table', 1),
(93, '2018_05_14_164754_add_opening_stock_permission', 1),
(94, '2018_05_15_134729_add_design_to_invoice_layouts', 1),
(95, '2018_05_16_183307_add_tax_fields_invoice_layout', 1),
(96, '2018_05_18_191956_add_sell_return_to_transaction_table', 1),
(97, '2018_05_21_131349_add_custom_fileds_to_contacts_table', 1),
(98, '2018_05_21_131607_invoice_layout_fields_for_sell_return', 1),
(99, '2018_05_21_131949_add_custom_fileds_and_website_to_business_locations_table', 1),
(100, '2018_05_22_123527_create_reference_counts_table', 1),
(101, '2018_05_22_154540_add_ref_no_prefixes_column_to_business_table', 1),
(102, '2018_05_24_132620_add_ref_no_column_to_transaction_payments_table', 1),
(103, '2018_05_24_161026_add_location_id_column_to_business_location_table', 1),
(104, '2018_05_25_180603_create_modifiers_related_table', 1),
(105, '2018_05_29_121714_add_purchase_line_id_to_stock_adjustment_line_table', 1),
(106, '2018_05_31_114645_add_res_order_status_column_to_transactions_table', 1),
(107, '2018_06_05_103530_rename_purchase_line_id_in_stock_adjustment_lines_table', 1),
(108, '2018_06_05_111905_modify_products_table_for_modifiers', 1),
(109, '2018_06_06_110524_add_parent_sell_line_id_column_to_transaction_sell_lines_table', 1),
(110, '2018_06_07_152443_add_is_service_staff_to_roles_table', 1),
(111, '2018_06_07_182258_add_image_field_to_products_table', 1),
(112, '2018_06_13_133705_create_bookings_table', 1),
(113, '2018_06_15_173636_add_email_column_to_contacts_table', 1),
(114, '2018_06_27_182835_add_superadmin_related_fields_business', 1),
(115, '2018_07_10_101913_add_custom_fields_to_products_table', 1),
(116, '2018_07_17_103434_add_sales_person_name_label_to_invoice_layouts_table', 1),
(117, '2018_07_17_163920_add_theme_skin_color_column_to_business_table', 1),
(118, '2018_07_24_160319_add_lot_no_line_id_to_transaction_sell_lines_table', 1),
(119, '2018_07_25_110004_add_show_expiry_and_show_lot_colums_to_invoice_layouts_table', 1),
(120, '2018_07_25_172004_add_discount_columns_to_transaction_sell_lines_table', 1),
(121, '2018_07_26_124720_change_design_column_type_in_invoice_layouts_table', 1),
(122, '2018_07_26_170424_add_unit_price_before_discount_column_to_transaction_sell_line_table', 1),
(123, '2018_07_28_103614_add_credit_limit_column_to_contacts_table', 1),
(124, '2018_08_08_110755_add_new_payment_methods_to_transaction_payments_table', 1),
(125, '2018_08_08_122225_modify_cash_register_transactions_table_for_new_payment_methods', 1),
(126, '2018_08_14_104036_add_opening_balance_type_to_transactions_table', 1),
(127, '2018_09_04_155900_create_accounts_table', 1),
(128, '2018_09_06_114438_create_selling_price_groups_table', 1),
(129, '2018_09_06_154057_create_variation_group_prices_table', 1),
(130, '2018_09_07_102413_add_permission_to_access_default_selling_price', 1),
(131, '2018_09_07_134858_add_selling_price_group_id_to_transactions_table', 1),
(132, '2018_09_10_112448_update_product_type_to_single_if_null_in_products_table', 1),
(133, '2018_09_10_152703_create_account_transactions_table', 1),
(134, '2018_09_10_173656_add_account_id_column_to_transaction_payments_table', 1),
(135, '2018_09_19_123914_create_notification_templates_table', 1),
(136, '2018_09_22_110504_add_sms_and_email_settings_columns_to_business_table', 1),
(137, '2018_09_24_134942_add_lot_no_line_id_to_stock_adjustment_lines_table', 1),
(138, '2018_09_26_105557_add_transaction_payments_for_existing_expenses', 1),
(139, '2018_09_27_111609_modify_transactions_table_for_purchase_return', 1),
(140, '2018_09_27_131154_add_quantity_returned_column_to_purchase_lines_table', 1),
(141, '2018_10_02_131401_add_return_quantity_column_to_transaction_sell_lines_table', 1),
(142, '2018_10_03_104918_add_qty_returned_column_to_transaction_sell_lines_purchase_lines_table', 1),
(143, '2018_10_03_185947_add_default_notification_templates_to_database', 1),
(144, '2018_10_09_153105_add_business_id_to_transaction_payments_table', 1),
(145, '2018_10_16_135229_create_permission_for_sells_and_purchase', 1),
(146, '2018_10_22_114441_add_columns_for_variable_product_modifications', 1),
(147, '2018_10_22_134428_modify_variable_product_data', 1),
(148, '2018_10_30_181558_add_table_tax_headings_to_invoice_layout', 1),
(149, '2018_10_31_122619_add_pay_terms_field_transactions_table', 1),
(150, '2018_10_31_161328_add_new_permissions_for_pos_screen', 1),
(151, '2018_10_31_174752_add_access_selected_contacts_only_to_users_table', 1),
(152, '2018_10_31_175627_add_user_contact_access', 1),
(153, '2018_10_31_180559_add_auto_send_sms_column_to_notification_templates_table', 1),
(154, '2018_11_02_171949_change_card_type_column_to_varchar_in_transaction_payments_table', 1),
(155, '2018_11_08_105621_add_role_permissions', 1),
(156, '2018_11_26_114135_add_is_suspend_column_to_transactions_table', 1),
(157, '2018_11_28_104410_modify_units_table_for_multi_unit', 1),
(158, '2018_11_28_170952_add_sub_unit_id_to_purchase_lines_and_sell_lines', 1),
(159, '2018_11_29_115918_add_primary_key_in_system_table', 1),
(160, '2018_12_03_185546_add_product_description_column_to_products_table', 1),
(161, '2018_12_06_114937_modify_system_table_and_users_table', 1),
(162, '2018_12_13_160007_add_custom_fields_display_options_to_invoice_layouts_table', 1),
(163, '2018_12_14_103307_modify_system_table', 1),
(164, '2018_12_18_133837_add_prev_balance_due_columns_to_invoice_layouts_table', 1),
(165, '2018_12_18_170656_add_invoice_token_column_to_transaction_table', 1),
(166, '2018_12_20_133639_add_date_time_format_column_to_invoice_layouts_table', 1),
(167, '2018_12_21_120659_add_recurring_invoice_fields_to_transactions_table', 1),
(168, '2018_12_24_154933_create_notifications_table', 1),
(169, '2019_01_08_112015_add_document_column_to_transaction_payments_table', 1),
(170, '2019_01_10_124645_add_account_permission', 1),
(171, '2019_01_16_125825_add_subscription_no_column_to_transactions_table', 1),
(172, '2019_01_28_111647_add_order_addresses_column_to_transactions_table', 1),
(173, '2019_02_13_173821_add_is_inactive_column_to_products_table', 1),
(174, '2019_02_19_103118_create_discounts_table', 1),
(175, '2019_02_21_120324_add_discount_id_column_to_transaction_sell_lines_table', 1),
(176, '2019_02_21_134324_add_permission_for_discount', 1),
(177, '2019_03_04_170832_add_service_staff_columns_to_transaction_sell_lines_table', 1),
(178, '2019_03_09_102425_add_sub_type_column_to_transactions_table', 1),
(179, '2019_03_09_124457_add_indexing_transaction_sell_lines_purchase_lines_table', 1),
(180, '2019_03_12_120336_create_activity_log_table', 1),
(181, '2019_03_15_132925_create_media_table', 1),
(182, '2019_05_08_130339_add_indexing_to_parent_id_in_transaction_payments_table', 1),
(183, '2019_05_10_132311_add_missing_column_indexing', 1),
(184, '2019_05_14_091812_add_show_image_column_to_invoice_layouts_table', 1),
(185, '2019_05_25_104922_add_view_purchase_price_permission', 1),
(186, '2019_06_17_103515_add_profile_informations_columns_to_users_table', 1),
(187, '2019_06_18_135524_add_permission_to_view_own_sales_only', 1),
(188, '2019_06_19_112058_add_database_changes_for_reward_points', 1),
(189, '2019_06_28_133732_change_type_column_to_string_in_transactions_table', 1),
(190, '2019_07_13_111420_add_is_created_from_api_column_to_transactions_table', 1),
(191, '2019_07_15_165136_add_fields_for_combo_product', 1),
(192, '2019_07_19_103446_add_mfg_quantity_used_column_to_purchase_lines_table', 1),
(193, '2019_07_22_152649_add_not_for_selling_in_product_table', 1),
(194, '2019_07_29_185351_add_show_reward_point_column_to_invoice_layouts_table', 1),
(195, '2019_08_08_162302_add_sub_units_related_fields', 1),
(196, '2019_08_26_133419_update_price_fields_decimal_point', 1),
(197, '2019_09_02_160054_remove_location_permissions_from_roles', 1),
(198, '2019_09_03_185259_add_permission_for_pos_screen', 1),
(199, '2019_09_04_163141_add_location_id_to_cash_registers_table', 1),
(200, '2019_09_04_184008_create_types_of_services_table', 1),
(201, '2019_09_06_131445_add_types_of_service_fields_to_transactions_table', 1),
(202, '2019_09_09_134810_add_default_selling_price_group_id_column_to_business_locations_table', 1),
(203, '2019_09_12_105616_create_product_locations_table', 1),
(204, '2019_09_17_122522_add_custom_labels_column_to_business_table', 1),
(205, '2019_09_18_164319_add_shipping_fields_to_transactions_table', 1),
(206, '2019_09_19_170927_close_all_active_registers', 1),
(207, '2019_09_23_161906_add_media_description_cloumn_to_media_table', 1),
(208, '2019_10_18_155633_create_account_types_table', 1),
(209, '2019_10_22_163335_add_common_settings_column_to_business_table', 1),
(210, '2019_10_29_132521_add_update_purchase_status_permission', 1),
(211, '2019_11_09_110522_add_indexing_to_lot_number', 1),
(212, '2019_11_19_170824_add_is_active_column_to_business_locations_table', 1),
(213, '2019_11_21_162913_change_quantity_field_types_to_decimal', 1),
(214, '2019_11_25_160340_modify_categories_table_for_polymerphic_relationship', 1),
(215, '2019_12_02_105025_create_warranties_table', 1),
(216, '2019_12_03_180342_add_common_settings_field_to_invoice_layouts_table', 1),
(217, '2019_12_05_183955_add_more_fields_to_users_table', 1),
(218, '2019_12_06_174904_add_change_return_label_column_to_invoice_layouts_table', 1),
(219, '2019_12_11_121307_add_draft_and_quotation_list_permissions', 1),
(220, '2019_12_12_180126_copy_expense_total_to_total_before_tax', 1),
(221, '2019_12_19_181412_make_alert_quantity_field_nullable_on_products_table', 1),
(222, '2019_12_25_173413_create_dashboard_configurations_table', 1),
(223, '2020_01_08_133506_create_document_and_notes_table', 1),
(224, '2020_01_09_113252_add_cc_bcc_column_to_notification_templates_table', 1),
(225, '2020_01_16_174818_add_round_off_amount_field_to_transactions_table', 1),
(226, '2020_01_28_162345_add_weighing_scale_settings_in_business_settings_table', 1),
(227, '2020_02_18_172447_add_import_fields_to_transactions_table', 1),
(228, '2020_03_13_135844_add_is_active_column_to_selling_price_groups_table', 1),
(229, '2020_03_16_115449_add_contact_status_field_to_contacts_table', 1),
(230, '2020_03_26_124736_add_allow_login_column_in_users_table', 1),
(231, '2020_04_13_154150_add_feature_products_column_to_business_loactions', 1),
(232, '2020_04_15_151802_add_user_type_to_users_table', 1),
(233, '2020_04_22_153905_add_subscription_repeat_on_column_to_transactions_table', 1),
(234, '2020_04_28_111436_add_shipping_address_to_contacts_table', 1),
(235, '2020_06_01_094654_add_max_sale_discount_column_to_users_table', 1),
(236, '2020_06_12_162245_modify_contacts_table', 1),
(237, '2020_06_22_103104_change_recur_interval_default_to_one', 1),
(238, '2020_07_09_174621_add_balance_field_to_contacts_table', 1),
(239, '2020_07_23_104933_change_status_column_to_varchar_in_transaction_table', 1),
(240, '2020_09_07_171059_change_completed_stock_transfer_status_to_final', 1),
(241, '2020_09_21_123224_modify_booking_status_column_in_bookings_table', 1),
(242, '2020_09_22_121639_create_discount_variations_table', 1),
(243, '2020_10_05_121550_modify_business_location_table_for_invoice_layout', 1),
(244, '2020_10_16_175726_set_status_as_received_for_opening_stock', 1),
(245, '2020_10_23_170823_add_for_group_tax_column_to_tax_rates_table', 1),
(246, '2020_11_04_130940_add_more_custom_fields_to_contacts_table', 1),
(247, '2020_11_10_152841_add_cash_register_permissions', 1),
(248, '2020_11_17_164041_modify_type_column_to_varchar_in_contacts_table', 1),
(249, '2020_12_18_181447_add_shipping_custom_fields_to_transactions_table', 1),
(250, '2020_12_22_164303_add_sub_status_column_to_transactions_table', 1),
(251, '2020_12_24_153050_add_custom_fields_to_transactions_table', 1),
(252, '2020_12_28_105403_add_whatsapp_text_column_to_notification_templates_table', 1),
(253, '2020_12_29_165925_add_model_document_type_to_media_table', 1),
(254, '2021_02_08_175632_add_contact_number_fields_to_users_table', 1),
(255, '2021_02_11_172217_add_indexing_for_multiple_columns', 1),
(256, '2021_02_23_122043_add_more_columns_to_customer_groups_table', 1),
(257, '2021_02_24_175551_add_print_invoice_permission_to_all_roles', 1),
(258, '2021_03_03_162021_add_purchase_order_columns_to_purchase_lines_and_transactions_table', 1),
(259, '2021_03_11_120229_add_sales_order_columns', 1),
(260, '2021_03_16_120705_add_business_id_to_activity_log_table', 1),
(261, '2021_03_16_153427_add_code_columns_to_business_table', 1),
(262, '2021_03_18_173308_add_account_details_column_to_accounts_table', 1),
(263, '2021_03_18_183119_add_prefer_payment_account_columns_to_transactions_table', 1),
(264, '2021_03_22_120810_add_more_types_of_service_custom_fields', 1),
(265, '2021_03_24_183132_add_shipping_export_custom_field_details_to_contacts_table', 1),
(266, '2021_03_25_170715_add_export_custom_fields_info_to_transactions_table', 1),
(267, '2021_04_15_063449_add_denominations_column_to_cash_registers_table', 1),
(268, '2021_05_22_083426_add_indexing_to_account_transactions_table', 1),
(269, '2021_07_08_065808_add_additional_expense_columns_to_transaction_table', 1),
(270, '2021_07_13_082918_add_qr_code_columns_to_invoice_layouts_table', 1),
(271, '2021_07_21_061615_add_fields_to_show_commission_agent_in_invoice_layout', 1),
(272, '2021_08_13_105549_add_crm_contact_id_to_users_table', 1),
(273, '2021_08_25_114932_add_payment_link_fields_to_transaction_payments_table', 1),
(274, '2021_09_01_063110_add_spg_column_to_discounts_table', 1),
(275, '2021_09_03_061528_modify_cash_register_transactions_table', 1),
(276, '2021_10_05_061658_add_source_column_to_transactions_table', 1),
(277, '2021_12_16_121851_add_parent_id_column_to_expense_categories_table', 1),
(278, '2022_04_14_075120_add_payment_type_column_to_transaction_payments_table', 1),
(279, '2022_04_21_083327_create_cash_denominations_table', 1),
(280, '2022_05_10_055307_add_delivery_date_column_to_transactions_table', 1),
(281, '2022_06_13_123135_add_currency_precision_and_quantity_precision_fields_to_business_table', 1),
(282, '2022_06_28_133342_add_secondary_unit_columns_to_products_sell_line_purchase_lines_tables', 1),
(283, '2022_07_13_114307_create_purchase_requisition_related_columns', 1),
(284, '2022_08_25_132707_add_service_staff_timer_fields_to_products_and_users_table', 1),
(285, '2023_01_28_114255_add_letter_head_column_to_invoice_layouts_table', 1),
(286, '2023_02_11_161510_add_event_column_to_activity_log_table', 1),
(287, '2023_02_11_161511_add_batch_uuid_column_to_activity_log_table', 1),
(288, '2023_03_02_170312_add_provider_to_oauth_clients_table', 1),
(289, '2023_03_21_122731_add_sale_invoice_scheme_id_business_table', 1),
(290, '2023_03_21_170446_add_number_type_to_invoice_scheme', 1),
(291, '2023_04_17_155216_add_custom_fields_to_products', 1),
(292, '2023_04_28_130247_add_price_type_to_group_price_table', 1),
(293, '2020_03_19_130231_add_contact_id_to_users_table', 2),
(294, '2020_03_27_133605_create_schedules_table', 2),
(295, '2020_03_27_133628_create_schedule_users_table', 2),
(296, '2020_03_30_112834_create_schedule_logs_table', 2),
(297, '2020_04_02_182331_add_crm_module_version_to_system_table', 2),
(298, '2020_04_08_153231_modify_cloumn_in_contacts_table', 2),
(299, '2020_04_09_101052_create_lead_users_table', 2),
(300, '2020_04_16_114747_create_crm_campaigns_table', 2),
(301, '2021_01_07_155757_add_followup_additional_info_column_to_crm_schedules_table', 2),
(302, '2021_02_02_140021_add_additional_info_to_crm_campaigns_table', 2),
(303, '2021_02_02_173651_add_new_columns_to_contacts_table', 2),
(304, '2021_02_04_120439_create_call_logs_table', 2),
(305, '2021_02_08_172047_add_mobile_name_column_to_crm_call_logs_table', 2),
(306, '2021_02_16_190038_add_crm_module_indexing', 2),
(307, '2021_02_19_120846_create_crm_followup_invoices', 2),
(308, '2021_02_22_132125_add_follow_up_by_to_crm_schedules_table', 2),
(309, '2021_03_24_160736_add_department_and_designation_to_users_table', 2),
(310, '2021_06_15_152924_create_proposal_templates_table', 2),
(311, '2021_06_16_114448_add_recursive_fields_to_crm_schedules_table', 2),
(312, '2021_06_16_125740_create_proposals_table', 2),
(313, '2021_09_24_065738_add_crm_settings_column_to_business_table', 2),
(314, '2022_02_09_055012_create_crm_marketplaces_table', 2),
(315, '2022_02_17_113045_add_source_id_to_marketplace', 2),
(316, '2022_03_02_180929_add_followup_category_id', 2),
(317, '2022_05_26_061553_create_crm_contact_person_commissions_table', 2),
(318, '2022_06_06_073006_add_cc_and_bcc_columns_to_crm_proposals_table', 2),
(319, '2019_07_15_114211_add_manufacturing_module_version_to_system_table', 3),
(320, '2019_07_15_114403_create_mfg_recipes_table', 3),
(321, '2019_07_18_180217_add_production_columns_to_transactions_table', 3),
(322, '2019_07_26_110753_add_manufacturing_settings_column_to_business_table', 3),
(323, '2019_07_26_170450_add_manufacturing_permissions', 3),
(324, '2019_08_08_110035_create_mfg_recipe_ingredients_table', 3),
(325, '2019_08_08_172837_add_recipe_add_edit_permissions', 3),
(326, '2019_08_12_114610_add_ingredient_waste_percent_columns', 3),
(327, '2019_11_05_115136_create_ingredient_groups_table', 3),
(328, '2020_02_22_120303_add_column_to_mfg_recipe_ingredients_table', 3),
(329, '2020_08_19_103831_add_production_cost_type_to_recipe_and_transaction_table', 3),
(330, '2021_02_16_190302_add_manufacturing_module_indexing', 3),
(331, '2021_04_07_154331_add_mfg_ingredient_group_id_to_transaction_sell_lines_table', 3),
(332, '2018_06_27_185405_create_packages_table', 4),
(333, '2018_06_28_182803_create_subscriptions_table', 4),
(334, '2018_07_17_182021_add_rows_to_system_table', 4),
(335, '2018_07_19_131721_add_options_to_packages_table', 4),
(336, '2018_08_17_155534_add_min_termination_alert_days', 4),
(337, '2018_08_28_105945_add_business_based_username_settings_to_system_table', 4),
(338, '2018_08_30_105906_add_superadmin_communicator_logs_table', 4),
(339, '2018_11_02_130636_add_custom_permissions_to_packages_table', 4),
(340, '2018_11_05_161848_add_more_fields_to_packages_table', 4),
(341, '2018_12_10_124621_modify_system_table_values_null_default', 4),
(342, '2019_05_10_135434_add_missing_database_column_indexes', 4),
(343, '2019_08_16_115300_create_superadmin_frontend_pages_table', 4),
(344, '2023_02_17_140135_AddVersionForAiAssistance', 5),
(345, '2023_02_21_182321_create_aiassistance_generation_table', 5),
(346, '2020_08_18_123107_add_connector_module_version_to_system_table', 6),
(347, '2020_08_19_175842_add_asset_management_module_version_to_system_table', 7),
(348, '2020_08_20_114339_create_assets_table', 7),
(349, '2020_08_20_173031_create_asset_transactions_table', 7),
(350, '2020_08_21_180138_add_asset_settings_column_to_business_table', 7),
(351, '2021_10_29_110841_create_asset_warranties_table', 7),
(352, '2022_03_26_062215_create_asset_maintenances_table', 7),
(353, '2022_05_11_070711_add_maintenance_note_column_to_asset_maintenances_table', 7),
(354, '2020_12_23_125610_add_spreadsheet_version_to_system_table', 8),
(355, '2020_12_23_153255_create_spreadsheets_table', 8),
(356, '2021_03_12_175416_create_spreadsheet_shares_table', 8),
(357, '2023_01_16_124948_add_folder_id_column_to_sheet_spreadsheets_table', 8),
(358, '2019_03_07_155813_make_repair_statuses_table', 9),
(359, '2019_03_08_120634_add_repair_columns_to_transactions_table', 9),
(360, '2019_03_14_182704_add_repair_permissions', 9),
(361, '2019_03_29_110241_add_repair_version_column_to_system_table', 9),
(362, '2019_04_12_113901_add_repair_settings_column_to_business_table', 9),
(363, '2020_05_05_125008_create_device_models_table', 9),
(364, '2020_05_06_103135_add_repair_model_id_column_to_products_table', 9),
(365, '2020_07_11_120308_add_columns_to_repair_statuses_table', 9),
(366, '2020_07_31_130737_create_job_sheets_table', 9),
(367, '2020_08_07_124241_add_job_sheet_id_to_transactions_table', 9),
(368, '2020_08_22_104640_add_email_template_field_to_repair_status_table', 9),
(369, '2020_10_19_131934_add_job_sheet_custom_fields_to_repair_job_sheets_table', 9),
(370, '2020_11_25_111050_add_parts_column_to_repair_job_sheets_table', 9),
(371, '2020_12_30_101842_add_use_for_repair_column_to_brands_table', 9),
(372, '2021_02_16_190423_add_repair_module_indexing', 9),
(373, '2022_12_23_162847_add_repair_jobsheet_settings_column_to_business_table', 9),
(374, '2019_11_12_163135_create_projects_table', 10),
(375, '2019_11_12_164431_create_project_members_table', 10),
(376, '2019_11_14_112230_create_project_tasks_table', 10),
(377, '2019_11_14_112258_create_project_task_members_table', 10),
(378, '2019_11_18_154617_create_project_task_comments_table', 10),
(379, '2019_11_19_134807_create_project_time_logs_table', 10),
(380, '2019_12_11_102549_add_more_fields_in_transactions_table', 10),
(381, '2019_12_11_102735_create_invoice_lines_table', 10),
(382, '2020_01_07_172852_add_project_permissions', 10),
(383, '2020_01_08_115422_add_project_module_version_to_system_table', 10),
(384, '2020_07_10_114514_set_location_id_on_existing_invoice', 10),
(385, '2018_10_01_151252_create_documents_table', 11),
(386, '2018_10_02_151803_create_document_shares_table', 11),
(387, '2018_10_09_134558_create_reminders_table', 11),
(388, '2018_11_16_170756_create_to_dos_table', 11),
(389, '2019_02_22_120329_essentials_messages', 11),
(390, '2019_02_22_161513_add_message_permissions', 11),
(391, '2019_03_29_164339_add_essentials_version_to_system_table', 11),
(392, '2019_05_17_153306_create_essentials_leave_types_table', 11),
(393, '2019_05_17_175921_create_essentials_leaves_table', 11),
(394, '2019_05_21_154517_add_essentials_settings_columns_to_business_table', 11),
(395, '2019_05_21_181653_create_table_essentials_attendance', 11),
(396, '2019_05_30_110049_create_essentials_payrolls_table', 11),
(397, '2019_06_04_105723_create_essentials_holidays_table', 11),
(398, '2019_06_28_134217_add_payroll_columns_to_transactions_table', 11),
(399, '2019_08_26_103520_add_approve_leave_permission', 11),
(400, '2019_08_27_103724_create_essentials_allowance_and_deduction_table', 11),
(401, '2019_08_27_105236_create_essentials_user_allowances_and_deductions', 11),
(402, '2019_09_20_115906_add_more_columns_to_essentials_to_dos_table', 11),
(403, '2019_09_23_120439_create_essentials_todo_comments_table', 11),
(404, '2019_12_05_170724_add_hrm_columns_to_users_table', 11),
(405, '2019_12_09_105809_add_allowance_and_deductions_permission', 11),
(406, '2020_03_28_152838_create_essentials_shift_table', 11),
(407, '2020_03_30_162029_create_user_shifts_table', 11),
(408, '2020_03_31_134558_add_shift_id_to_attendance_table', 11),
(409, '2020_11_05_105157_modify_todos_date_column_type', 11),
(410, '2020_11_11_174852_add_end_time_column_to_essentials_reminders_table', 11),
(411, '2020_11_26_170527_create_essentials_kb_table', 11),
(412, '2020_11_30_112615_create_essentials_kb_users_table', 11),
(413, '2021_02_12_185514_add_clock_in_location_to_essentials_attendances_table', 11),
(414, '2021_02_16_190203_add_essentials_module_indexing', 11),
(415, '2021_02_27_133448_add_columns_to_users_table', 11),
(416, '2021_03_04_174857_create_payroll_groups_table', 11),
(417, '2021_03_04_175025_create_payroll_group_transactions_table', 11),
(418, '2021_03_09_123914_add_auto_clockout_to_essentials_shifts', 11),
(419, '2021_06_17_121451_add_location_id_to_table', 11),
(420, '2021_09_28_091541_create_essentials_user_sales_targets_table', 11),
(421, '2020_09_29_184909_add_product_catalogue_version', 12),
(422, '2018_10_10_110400_add_module_version_to_system_table', 13),
(423, '2018_10_10_122845_add_woocommerce_api_settings_to_business_table', 13),
(424, '2018_10_10_162041_add_woocommerce_category_id_to_categories_table', 13),
(425, '2018_10_11_173839_create_woocommerce_sync_logs_table', 13),
(426, '2018_10_16_123522_add_woocommerce_tax_rate_id_column_to_tax_rates_table', 13),
(427, '2018_10_23_111555_add_woocommerce_attr_id_column_to_variation_templates_table', 13),
(428, '2018_12_03_163945_add_woocommerce_permissions', 13),
(429, '2019_02_18_154414_change_woocommerce_sync_logs_table', 13),
(430, '2019_04_19_174129_add_disable_woocommerce_sync_column_to_products_table', 13),
(431, '2019_06_08_132440_add_woocommerce_wh_oc_secret_column_to_business_table', 13),
(432, '2019_10_01_171828_add_woocommerce_media_id_columns', 13),
(433, '2020_09_07_124952_add_woocommerce_skipped_orders_fields_to_business_table', 13),
(434, '2021_02_16_190608_add_woocommerce_module_indexing', 13),
(435, '2019_07_07_093258_create_chart_of_accounts_table', 14),
(436, '2019_07_07_093648_create_journal_entries_table', 14),
(437, '2019_07_07_110645_create_payment_types_table', 14),
(438, '2021_08_23_175321_add_contact_and_location_id_to_journal_entries_table', 14),
(439, '2021_11_29_170819_add_business_id_to_chart_of_accounts_table', 14),
(440, '2022_01_17_202319_create_payment_details_table', 14),
(441, '2022_01_19_034231_create_countries_table', 14),
(442, '2022_02_01_031031_create_transfers_table', 14),
(443, '2022_02_03_215602_create_budgets_table', 14),
(444, '2022_02_08_113906_add_opening_balance_to_chart_of_accounts_table', 14),
(445, '2022_02_08_121045_add_currency_id_to_chart_of_accounts_table', 14),
(446, '2022_02_09_002406_add_payment_type_id_to_chart_of_accounts_table', 14),
(447, '2022_02_09_125328_create_account_detail_types_table', 14),
(448, '2022_02_09_223848_create_account_subtypes_table', 14),
(449, '2022_02_09_223849_add_account_subtype_id_and_detail_type_id_to_chart_of_accounts_table', 14),
(450, '2022_02_23_130555_add_journal_entry_id_to_transactions_table', 14),
(451, '2022_03_17_140457_add_reconcile_opening_balance_to_chart_of_accounts_table', 14),
(452, '2022_04_11_163625_populate_account_subtypes_table', 14),
(453, '2022_04_11_165143_populate_account_detail_types_table', 14),
(454, '2022_06_08_105942_create_branch_capital_table', 14),
(455, '2022_07_25_100234_change_payment_type_id_column_from_int_to_string_in_payment_details_table', 14),
(456, '2022_08_04_143146_create_cms_pages_table', 15),
(457, '2022_09_10_161849_add_layout_column_to_cms_pages_table', 15),
(458, '2022_09_10_163209_create_cms_site_details_table', 15),
(459, '2022_09_15_122547_create_cms_page_metas_table', 15),
(460, '2022_09_16_130337_create_default_data_for_cms', 15);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `template_for` varchar(191) NOT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `whatsapp_text` text DEFAULT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `cc` varchar(191) DEFAULT NULL,
  `bcc` varchar(191) DEFAULT NULL,
  `auto_send` tinyint(1) NOT NULL DEFAULT 0,
  `auto_send_sms` tinyint(1) NOT NULL DEFAULT 0,
  `auto_send_wa_notif` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_templates`
--

INSERT INTO `notification_templates` (`id`, `business_id`, `template_for`, `email_body`, `sms_body`, `whatsapp_text`, `subject`, `cc`, `bcc`, `auto_send`, `auto_send_sms`, `auto_send_wa_notif`, `created_at`, `updated_at`) VALUES
(1, 1, 'new_sale', '<p>Dear {contact_name},</p>\n\n                    <p>Your invoice number is {invoice_number}<br />\n                    Total amount: {total_amount}<br />\n                    Paid amount: {received_amount}</p>\n\n                    <p>Thank you for shopping with us.</p>\n\n                    <p>{business_logo}</p>\n\n                    <p>&nbsp;</p>', 'Dear {contact_name}, Thank you for shopping with us. {business_name}', NULL, 'Thank you from {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(2, 1, 'payment_received', '<p>Dear {contact_name},</p>\n\n                <p>We have received a payment of {received_amount}</p>\n\n                <p>{business_logo}</p>', 'Dear {contact_name}, We have received a payment of {received_amount}. {business_name}', NULL, 'Payment Received, from {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(3, 1, 'payment_reminder', '<p>Dear {contact_name},</p>\n\n                    <p>This is to remind you that you have pending payment of {due_amount}. Kindly pay it as soon as possible.</p>\n\n                    <p>{business_logo}</p>', 'Dear {contact_name}, You have pending payment of {due_amount}. Kindly pay it as soon as possible. {business_name}', NULL, 'Payment Reminder, from {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(4, 1, 'new_booking', '<p>Dear {contact_name},</p>\n\n                    <p>Your booking is confirmed</p>\n\n                    <p>Date: {start_time} to {end_time}</p>\n\n                    <p>Table: {table}</p>\n\n                    <p>Location: {location}</p>\n\n                    <p>{business_logo}</p>', 'Dear {contact_name}, Your booking is confirmed. Date: {start_time} to {end_time}, Table: {table}, Location: {location}', NULL, 'Booking Confirmed - {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(5, 1, 'new_order', '<p>Dear {contact_name},</p>\n\n                    <p>We have a new order with reference number {order_ref_number}. Kindly process the products as soon as possible.</p>\n\n                    <p>{business_name}<br />\n                    {business_logo}</p>', 'Dear {contact_name}, We have a new order with reference number {order_ref_number}. Kindly process the products as soon as possible. {business_name}', NULL, 'New Order, from {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(6, 1, 'payment_paid', '<p>Dear {contact_name},</p>\n\n                    <p>We have paid amount {paid_amount} again invoice number {order_ref_number}.<br />\n                    Kindly note it down.</p>\n\n                    <p>{business_name}<br />\n                    {business_logo}</p>', 'We have paid amount {paid_amount} again invoice number {order_ref_number}.\n                    Kindly note it down. {business_name}', NULL, 'Payment Paid, from {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(7, 1, 'items_received', '<p>Dear {contact_name},</p>\n\n                    <p>We have received all items from invoice reference number {order_ref_number}. Thank you for processing it.</p>\n\n                    <p>{business_name}<br />\n                    {business_logo}</p>', 'We have received all items from invoice reference number {order_ref_number}. Thank you for processing it. {business_name}', NULL, 'Items received, from {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(8, 1, 'items_pending', '<p>Dear {contact_name},<br />\n                    This is to remind you that we have not yet received some items from invoice reference number {order_ref_number}. Please process it as soon as possible.</p>\n\n                    <p>{business_name}<br />\n                    {business_logo}</p>', 'This is to remind you that we have not yet received some items from invoice reference number {order_ref_number} . Please process it as soon as possible.{business_name}', NULL, 'Items Pending, from {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(9, 1, 'new_quotation', '<p>Dear {contact_name},</p>\n\n                    <p>Your quotation number is {invoice_number}<br />\n                    Total amount: {total_amount}</p>\n\n                    <p>Thank you for shopping with us.</p>\n\n                    <p>{business_logo}</p>\n\n                    <p>&nbsp;</p>', 'Dear {contact_name}, Thank you for shopping with us. {business_name}', NULL, 'Thank you from {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(10, 1, 'purchase_order', '<p>Dear {contact_name},</p>\n\n                    <p>We have a new purchase order with reference number {order_ref_number}. The respective invoice is attached here with.</p>\n\n                    <p>{business_logo}</p>', 'We have a new purchase order with reference number {order_ref_number}. {business_name}', NULL, 'New Purchase Order, from {business_name}', NULL, NULL, 0, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `secret` varchar(100) NOT NULL,
  `provider` varchar(191) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Nacional Code Personal Access Client', '2E0DN32oblPWz6t0wtSHhJJNHU8fniUWjzj7fmfE', NULL, 'http://localhost', 1, 0, 0, '2023-06-16 15:07:13', '2023-06-16 15:07:13'),
(2, NULL, 'Nacional Code Password Grant Client', 'uym7uq2JxF7g3gk2rMFwqqoBTMJQ6117q8jKZSIh', 'users', 'http://localhost', 0, 1, 0, '2023-06-16 15:07:13', '2023-06-16 15:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2023-06-16 15:07:13', '2023-06-16 15:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `location_count` int(11) NOT NULL COMMENT 'No. of Business Locations, 0 = infinite option.',
  `user_count` int(11) NOT NULL,
  `product_count` int(11) NOT NULL,
  `bookings` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Enable/Disable bookings',
  `kitchen` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Enable/Disable kitchen',
  `order_screen` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Enable/Disable order_screen',
  `tables` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Enable/Disable tables',
  `invoice_count` int(11) NOT NULL,
  `interval` enum('days','months','years') NOT NULL,
  `interval_count` int(11) NOT NULL,
  `trial_days` int(11) NOT NULL,
  `price` decimal(22,4) NOT NULL,
  `custom_permissions` longtext NOT NULL,
  `created_by` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `is_one_time` tinyint(1) NOT NULL DEFAULT 0,
  `enable_custom_link` tinyint(1) NOT NULL DEFAULT 0,
  `custom_link` varchar(191) DEFAULT NULL,
  `custom_link_text` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `payment_type_id` varchar(11) DEFAULT NULL,
  `transaction_type` varchar(191) DEFAULT NULL,
  `reference` int(11) DEFAULT NULL,
  `cheque_number` varchar(191) DEFAULT NULL,
  `receipt` varchar(191) DEFAULT NULL,
  `account_number` varchar(191) DEFAULT NULL,
  `bank_name` varchar(191) DEFAULT NULL,
  `routing_code` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_types`
--

CREATE TABLE `payment_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `system_name` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_cash` tinyint(4) NOT NULL DEFAULT 0,
  `is_online` tinyint(4) NOT NULL DEFAULT 0,
  `is_system` tinyint(4) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `position` int(11) DEFAULT NULL,
  `options` text DEFAULT NULL,
  `unique_id` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_types`
--

INSERT INTO `payment_types` (`id`, `business_id`, `name`, `system_name`, `description`, `is_cash`, `is_online`, `is_system`, `active`, `position`, `options`, `unique_id`, `created_at`, `updated_at`) VALUES
(1, 0, 'Cash', NULL, NULL, 0, 0, 0, 1, NULL, NULL, NULL, '2023-06-16 00:00:00', '2023-06-16 00:00:00'),
(2, 0, 'Ecocash', NULL, 'ecocash', 0, 0, 0, 1, 1, NULL, NULL, '2023-06-16 00:00:00', '2023-06-16 00:00:00'),
(3, 0, 'Mpesa', NULL, 'Pay via Mpesa', 0, 0, 0, 1, NULL, NULL, NULL, '2023-06-16 00:00:00', '2023-06-16 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'profit_loss_report.view', 'web', '2023-06-17 01:32:43', NULL),
(2, 'direct_sell.access', 'web', '2023-06-17 01:32:43', NULL),
(3, 'product.opening_stock', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(4, 'crud_all_bookings', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(5, 'crud_own_bookings', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(6, 'access_default_selling_price', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(7, 'purchase.payments', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(8, 'sell.payments', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(9, 'edit_product_price_from_sale_screen', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(10, 'edit_product_discount_from_sale_screen', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(11, 'roles.view', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(12, 'roles.create', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(13, 'roles.update', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(14, 'roles.delete', 'web', '2023-06-17 01:32:43', '2023-06-17 01:32:43'),
(15, 'account.access', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(16, 'discount.access', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(17, 'view_purchase_price', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(18, 'view_own_sell_only', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(19, 'edit_product_discount_from_pos_screen', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(20, 'edit_product_price_from_pos_screen', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(21, 'access_shipping', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(22, 'purchase.update_status', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(23, 'list_drafts', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(24, 'list_quotations', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(25, 'view_cash_register', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(26, 'close_cash_register', 'web', '2023-06-17 01:32:44', '2023-06-17 01:32:44'),
(27, 'print_invoice', 'web', '2023-06-17 01:32:45', '2023-06-17 01:32:45'),
(28, 'user.view', 'web', '2023-06-17 01:32:45', NULL),
(29, 'user.create', 'web', '2023-06-17 01:32:45', NULL),
(30, 'user.update', 'web', '2023-06-17 01:32:45', NULL),
(31, 'user.delete', 'web', '2023-06-17 01:32:45', NULL),
(32, 'supplier.view', 'web', '2023-06-17 01:32:45', NULL),
(33, 'supplier.create', 'web', '2023-06-17 01:32:45', NULL),
(34, 'supplier.update', 'web', '2023-06-17 01:32:45', NULL),
(35, 'supplier.delete', 'web', '2023-06-17 01:32:45', NULL),
(36, 'customer.view', 'web', '2023-06-17 01:32:45', NULL),
(37, 'customer.create', 'web', '2023-06-17 01:32:45', NULL),
(38, 'customer.update', 'web', '2023-06-17 01:32:45', NULL),
(39, 'customer.delete', 'web', '2023-06-17 01:32:45', NULL),
(40, 'product.view', 'web', '2023-06-17 01:32:45', NULL),
(41, 'product.create', 'web', '2023-06-17 01:32:45', NULL),
(42, 'product.update', 'web', '2023-06-17 01:32:45', NULL),
(43, 'product.delete', 'web', '2023-06-17 01:32:45', NULL),
(44, 'purchase.view', 'web', '2023-06-17 01:32:45', NULL),
(45, 'purchase.create', 'web', '2023-06-17 01:32:45', NULL),
(46, 'purchase.update', 'web', '2023-06-17 01:32:45', NULL),
(47, 'purchase.delete', 'web', '2023-06-17 01:32:45', NULL),
(48, 'sell.view', 'web', '2023-06-17 01:32:45', NULL),
(49, 'sell.create', 'web', '2023-06-17 01:32:45', NULL),
(50, 'sell.update', 'web', '2023-06-17 01:32:45', NULL),
(51, 'sell.delete', 'web', '2023-06-17 01:32:45', NULL),
(52, 'purchase_n_sell_report.view', 'web', '2023-06-17 01:32:45', NULL),
(53, 'contacts_report.view', 'web', '2023-06-17 01:32:45', NULL),
(54, 'stock_report.view', 'web', '2023-06-17 01:32:45', NULL),
(55, 'tax_report.view', 'web', '2023-06-17 01:32:45', NULL),
(56, 'trending_product_report.view', 'web', '2023-06-17 01:32:45', NULL),
(57, 'register_report.view', 'web', '2023-06-17 01:32:45', NULL),
(58, 'sales_representative.view', 'web', '2023-06-17 01:32:45', NULL),
(59, 'expense_report.view', 'web', '2023-06-17 01:32:45', NULL),
(60, 'business_settings.access', 'web', '2023-06-17 01:32:45', NULL),
(61, 'barcode_settings.access', 'web', '2023-06-17 01:32:45', NULL),
(62, 'invoice_settings.access', 'web', '2023-06-17 01:32:45', NULL),
(63, 'brand.view', 'web', '2023-06-17 01:32:45', NULL),
(64, 'brand.create', 'web', '2023-06-17 01:32:45', NULL),
(65, 'brand.update', 'web', '2023-06-17 01:32:45', NULL),
(66, 'brand.delete', 'web', '2023-06-17 01:32:45', NULL),
(67, 'tax_rate.view', 'web', '2023-06-17 01:32:45', NULL),
(68, 'tax_rate.create', 'web', '2023-06-17 01:32:45', NULL),
(69, 'tax_rate.update', 'web', '2023-06-17 01:32:45', NULL),
(70, 'tax_rate.delete', 'web', '2023-06-17 01:32:45', NULL),
(71, 'unit.view', 'web', '2023-06-17 01:32:45', NULL),
(72, 'unit.create', 'web', '2023-06-17 01:32:45', NULL),
(73, 'unit.update', 'web', '2023-06-17 01:32:45', NULL),
(74, 'unit.delete', 'web', '2023-06-17 01:32:45', NULL),
(75, 'category.view', 'web', '2023-06-17 01:32:45', NULL),
(76, 'category.create', 'web', '2023-06-17 01:32:45', NULL),
(77, 'category.update', 'web', '2023-06-17 01:32:45', NULL),
(78, 'category.delete', 'web', '2023-06-17 01:32:45', NULL),
(79, 'expense.access', 'web', '2023-06-17 01:32:45', NULL),
(80, 'access_all_locations', 'web', '2023-06-17 01:32:45', NULL),
(81, 'dashboard.data', 'web', '2023-06-17 01:32:45', NULL),
(82, 'location.1', 'web', '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(83, 'manufacturing.access_recipe', 'web', '2023-06-16 15:04:53', '2023-06-16 15:04:53'),
(84, 'manufacturing.access_production', 'web', '2023-06-16 15:04:53', '2023-06-16 15:04:53'),
(85, 'manufacturing.add_recipe', 'web', '2023-06-16 15:04:53', '2023-06-16 15:04:53'),
(86, 'manufacturing.edit_recipe', 'web', '2023-06-16 15:04:53', '2023-06-16 15:04:53'),
(87, 'repair.create', 'web', '2023-06-16 15:08:44', '2023-06-16 15:08:44'),
(88, 'repair.update', 'web', '2023-06-16 15:08:44', '2023-06-16 15:08:44'),
(89, 'repair.view', 'web', '2023-06-16 15:08:44', '2023-06-16 15:08:44'),
(90, 'repair.delete', 'web', '2023-06-16 15:08:44', '2023-06-16 15:08:44'),
(91, 'repair_status.update', 'web', '2023-06-16 15:08:44', '2023-06-16 15:08:44'),
(92, 'repair_status.access', 'web', '2023-06-16 15:08:44', '2023-06-16 15:08:44'),
(93, 'project.create_project', 'web', '2023-06-16 15:09:22', '2023-06-16 15:09:22'),
(94, 'project.edit_project', 'web', '2023-06-16 15:09:22', '2023-06-16 15:09:22'),
(95, 'project.delete_project', 'web', '2023-06-16 15:09:22', '2023-06-16 15:09:22'),
(96, 'essentials.create_message', 'web', '2023-06-16 15:09:55', '2023-06-16 15:09:55'),
(97, 'essentials.view_message', 'web', '2023-06-16 15:09:55', '2023-06-16 15:09:55'),
(98, 'essentials.approve_leave', 'web', '2023-06-16 15:09:55', '2023-06-16 15:09:55'),
(99, 'essentials.assign_todos', 'web', '2023-06-16 15:09:56', '2023-06-16 15:09:56'),
(100, 'essentials.add_allowance_and_deduction', 'web', '2023-06-16 15:09:56', '2023-06-16 15:09:56'),
(101, 'woocommerce.syc_categories', 'web', '2023-06-16 15:13:50', '2023-06-16 15:13:50'),
(102, 'woocommerce.sync_products', 'web', '2023-06-16 15:13:50', '2023-06-16 15:13:50'),
(103, 'woocommerce.sync_orders', 'web', '2023-06-16 15:13:50', '2023-06-16 15:13:50'),
(104, 'woocommerce.map_tax_rates', 'web', '2023-06-16 15:13:50', '2023-06-16 15:13:50'),
(105, 'woocommerce.access_woocommerce_api_settings', 'web', '2023-06-16 15:13:50', '2023-06-16 15:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `pjt_invoice_lines`
--

CREATE TABLE `pjt_invoice_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `task` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `rate` decimal(22,4) NOT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `quantity` decimal(22,4) NOT NULL,
  `total` decimal(22,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pjt_projects`
--

CREATE TABLE `pjt_projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `status` enum('not_started','in_progress','on_hold','cancelled','completed') NOT NULL,
  `lead_id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `settings` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pjt_project_members`
--

CREATE TABLE `pjt_project_members` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pjt_project_tasks`
--

CREATE TABLE `pjt_project_tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `task_id` varchar(191) NOT NULL,
  `subject` varchar(191) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'low',
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `status` enum('completed','not_started','in_progress','on_hold','cancelled') NOT NULL DEFAULT 'not_started',
  `custom_field_1` varchar(191) DEFAULT NULL,
  `custom_field_2` varchar(191) DEFAULT NULL,
  `custom_field_3` varchar(191) DEFAULT NULL,
  `custom_field_4` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pjt_project_task_comments`
--

CREATE TABLE `pjt_project_task_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_task_id` int(10) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `commented_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pjt_project_task_members`
--

CREATE TABLE `pjt_project_task_members` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_task_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pjt_project_time_logs`
--

CREATE TABLE `pjt_project_time_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `project_task_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `printers`
--

CREATE TABLE `printers` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `connection_type` enum('network','windows','linux') NOT NULL,
  `capability_profile` enum('default','simple','SP2000','TEP-200M','P822D') NOT NULL DEFAULT 'default',
  `char_per_line` varchar(191) DEFAULT NULL,
  `ip_address` varchar(191) DEFAULT NULL,
  `port` varchar(191) DEFAULT NULL,
  `path` varchar(191) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `type` enum('single','variable','modifier','combo') DEFAULT NULL,
  `unit_id` int(11) UNSIGNED DEFAULT NULL,
  `secondary_unit_id` int(11) DEFAULT NULL,
  `sub_unit_ids` text DEFAULT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `sub_category_id` int(10) UNSIGNED DEFAULT NULL,
  `tax` int(10) UNSIGNED DEFAULT NULL,
  `tax_type` enum('inclusive','exclusive') NOT NULL,
  `enable_stock` tinyint(1) NOT NULL DEFAULT 0,
  `alert_quantity` decimal(22,4) DEFAULT NULL,
  `sku` varchar(191) NOT NULL,
  `barcode_type` enum('C39','C128','EAN13','EAN8','UPCA','UPCE') DEFAULT 'C128',
  `expiry_period` decimal(4,2) DEFAULT NULL,
  `expiry_period_type` enum('days','months') DEFAULT NULL,
  `enable_sr_no` tinyint(1) NOT NULL DEFAULT 0,
  `weight` varchar(191) DEFAULT NULL,
  `product_custom_field1` varchar(191) DEFAULT NULL,
  `product_custom_field2` varchar(191) DEFAULT NULL,
  `product_custom_field3` varchar(191) DEFAULT NULL,
  `product_custom_field4` varchar(191) DEFAULT NULL,
  `product_custom_field5` varchar(191) DEFAULT NULL,
  `product_custom_field6` varchar(191) DEFAULT NULL,
  `product_custom_field7` varchar(191) DEFAULT NULL,
  `product_custom_field8` varchar(191) DEFAULT NULL,
  `product_custom_field9` varchar(191) DEFAULT NULL,
  `product_custom_field10` varchar(191) DEFAULT NULL,
  `product_custom_field11` varchar(191) DEFAULT NULL,
  `product_custom_field12` varchar(191) DEFAULT NULL,
  `product_custom_field13` varchar(191) DEFAULT NULL,
  `product_custom_field14` varchar(191) DEFAULT NULL,
  `product_custom_field15` varchar(191) DEFAULT NULL,
  `product_custom_field16` varchar(191) DEFAULT NULL,
  `product_custom_field17` varchar(191) DEFAULT NULL,
  `product_custom_field18` varchar(191) DEFAULT NULL,
  `product_custom_field19` varchar(191) DEFAULT NULL,
  `product_custom_field20` varchar(191) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `woocommerce_media_id` int(11) DEFAULT NULL,
  `product_description` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `woocommerce_product_id` int(11) DEFAULT NULL,
  `woocommerce_disable_sync` tinyint(1) NOT NULL DEFAULT 0,
  `preparation_time_in_minutes` int(11) DEFAULT NULL,
  `warranty_id` int(11) DEFAULT NULL,
  `is_inactive` tinyint(1) NOT NULL DEFAULT 0,
  `repair_model_id` int(10) UNSIGNED DEFAULT NULL,
  `not_for_selling` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_locations`
--

CREATE TABLE `product_locations` (
  `product_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_racks`
--

CREATE TABLE `product_racks` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `rack` varchar(191) DEFAULT NULL,
  `row` varchar(191) DEFAULT NULL,
  `position` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variations`
--

CREATE TABLE `product_variations` (
  `id` int(10) UNSIGNED NOT NULL,
  `variation_template_id` int(11) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `is_dummy` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_lines`
--

CREATE TABLE `purchase_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `secondary_unit_quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `pp_without_discount` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Purchase price before inline discounts',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Inline discount percentage',
  `purchase_price` decimal(22,4) NOT NULL,
  `purchase_price_inc_tax` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `item_tax` decimal(22,4) NOT NULL COMMENT 'Tax for one quantity',
  `tax_id` int(10) UNSIGNED DEFAULT NULL,
  `purchase_requisition_line_id` int(11) DEFAULT NULL,
  `purchase_order_line_id` int(11) DEFAULT NULL,
  `quantity_sold` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Quanity sold from this purchase line',
  `quantity_adjusted` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Quanity adjusted in stock adjustment from this purchase line',
  `quantity_returned` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `po_quantity_purchased` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `mfg_quantity_used` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `mfg_date` date DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `lot_number` varchar(191) DEFAULT NULL,
  `sub_unit_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reference_counts`
--

CREATE TABLE `reference_counts` (
  `id` int(10) UNSIGNED NOT NULL,
  `ref_type` varchar(191) NOT NULL,
  `ref_count` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reference_counts`
--

INSERT INTO `reference_counts` (`id`, `ref_type`, `ref_count`, `business_id`, `created_at`, `updated_at`) VALUES
(1, 'contacts', 1, 1, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(2, 'business_location', 1, 1, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `repair_device_models`
--

CREATE TABLE `repair_device_models` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `repair_checklist` text DEFAULT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `device_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repair_job_sheets`
--

CREATE TABLE `repair_job_sheets` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `job_sheet_no` varchar(191) NOT NULL,
  `service_type` enum('carry_in','pick_up','on_site') NOT NULL,
  `pick_up_on_site_addr` text DEFAULT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `device_id` int(10) UNSIGNED DEFAULT NULL,
  `device_model_id` int(10) UNSIGNED DEFAULT NULL,
  `checklist` text DEFAULT NULL,
  `security_pwd` varchar(191) DEFAULT NULL,
  `security_pattern` varchar(191) DEFAULT NULL,
  `serial_no` varchar(191) NOT NULL,
  `status_id` int(11) NOT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `product_configuration` text DEFAULT NULL,
  `defects` text DEFAULT NULL,
  `product_condition` text DEFAULT NULL,
  `service_staff` int(10) UNSIGNED DEFAULT NULL,
  `comment_by_ss` text DEFAULT NULL COMMENT 'comment made by technician',
  `estimated_cost` decimal(22,4) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `parts` text DEFAULT NULL,
  `custom_field_1` varchar(191) DEFAULT NULL,
  `custom_field_2` varchar(191) DEFAULT NULL,
  `custom_field_3` varchar(191) DEFAULT NULL,
  `custom_field_4` varchar(191) DEFAULT NULL,
  `custom_field_5` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repair_statuses`
--

CREATE TABLE `repair_statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `color` varchar(191) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `is_completed_status` tinyint(1) NOT NULL DEFAULT 0,
  `sms_template` text DEFAULT NULL,
  `email_subject` text DEFAULT NULL,
  `email_body` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `res_product_modifier_sets`
--

CREATE TABLE `res_product_modifier_sets` (
  `modifier_set_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL COMMENT 'Table use to store the modifier sets applicable for a product'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `res_tables`
--

CREATE TABLE `res_tables` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_service_staff` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `business_id`, `is_default`, `is_service_staff`, `created_at`, `updated_at`) VALUES
(1, 'Admin#1', 'web', 1, 1, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40'),
(2, 'Cashier#1', 'web', 1, 0, 0, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(25, 2),
(26, 2),
(48, 2),
(49, 2),
(50, 2),
(51, 2),
(80, 2);

-- --------------------------------------------------------

--
-- Table structure for table `selling_price_groups`
--

CREATE TABLE `selling_price_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sell_line_warranties`
--

CREATE TABLE `sell_line_warranties` (
  `sell_line_id` int(11) NOT NULL,
  `warranty_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sheet_spreadsheets`
--

CREATE TABLE `sheet_spreadsheets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `sheet_data` longtext NOT NULL,
  `created_by` int(11) NOT NULL,
  `folder_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sheet_spreadsheet_shares`
--

CREATE TABLE `sheet_spreadsheet_shares` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sheet_spreadsheet_id` bigint(20) UNSIGNED NOT NULL,
  `shared_with` varchar(191) NOT NULL COMMENT 'Shared with like user/role/todo',
  `shared_id` int(11) NOT NULL COMMENT 'Id of shared with like user_id/role_id/todo_id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments_temp`
--

CREATE TABLE `stock_adjustments_temp` (
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustment_lines`
--

CREATE TABLE `stock_adjustment_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(22,4) NOT NULL,
  `secondary_unit_quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `unit_price` decimal(22,4) DEFAULT NULL COMMENT 'Last purchase unit price',
  `removed_purchase_line` int(11) DEFAULT NULL,
  `lot_no_line_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `package_id` int(10) UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `trial_end_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `package_price` decimal(22,4) NOT NULL,
  `package_details` longtext NOT NULL,
  `created_id` int(10) UNSIGNED NOT NULL,
  `paid_via` varchar(191) DEFAULT NULL,
  `payment_transaction_id` varchar(191) DEFAULT NULL,
  `status` enum('approved','waiting','declined') NOT NULL DEFAULT 'waiting',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `superadmin_communicator_logs`
--

CREATE TABLE `superadmin_communicator_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_ids` text DEFAULT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `superadmin_frontend_pages`
--

CREATE TABLE `superadmin_frontend_pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) DEFAULT NULL,
  `slug` varchar(191) NOT NULL,
  `content` longtext NOT NULL,
  `is_shown` tinyint(1) NOT NULL,
  `menu_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE `system` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system`
--

INSERT INTO `system` (`id`, `key`, `value`) VALUES
(1, 'db_version', '5.1.1'),
(2, 'default_business_active_status', '1'),
(3, 'crm_version', '2.1'),
(4, 'manufacturing_version', '3.0'),
(5, 'superadmin_version', '4.0'),
(6, 'app_currency_id', '2'),
(7, 'invoice_business_name', 'Nacional Code'),
(8, 'invoice_business_landmark', 'Landmark'),
(9, 'invoice_business_zip', 'Zip'),
(10, 'invoice_business_state', 'State'),
(11, 'invoice_business_city', 'City'),
(12, 'invoice_business_country', 'Country'),
(13, 'email', 'superadmin@example.com'),
(14, 'package_expiry_alert_days', '5'),
(15, 'enable_business_based_username', '0'),
(16, 'aiassistance_version', '1.1'),
(17, 'connector_version', '2.0'),
(18, 'assetmanagement_version', '2.0'),
(19, 'spreadsheet_version', '1.0'),
(20, 'repair_version', '2.0'),
(21, 'project_version', '2.1'),
(22, 'essentials_version', '4.0'),
(23, 'productcatalogue_version', '1.0'),
(24, 'woocommerce_version', '4.0'),
(25, 'accounting_version', '1.3.1'),
(26, 'cms_version', '1.0');

-- --------------------------------------------------------

--
-- Table structure for table `tax_rates`
--

CREATE TABLE `tax_rates` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `amount` double(22,4) NOT NULL,
  `is_tax_group` tinyint(1) NOT NULL DEFAULT 0,
  `for_tax_group` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(10) UNSIGNED NOT NULL,
  `woocommerce_tax_rate_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `journal_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `res_table_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'fields to restaurant module',
  `res_waiter_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'fields to restaurant module',
  `res_order_status` enum('received','cooked','served') DEFAULT NULL,
  `type` varchar(191) DEFAULT NULL,
  `sub_type` varchar(20) DEFAULT NULL,
  `status` varchar(191) NOT NULL,
  `sub_status` varchar(191) DEFAULT NULL,
  `is_quotation` tinyint(1) NOT NULL DEFAULT 0,
  `payment_status` enum('paid','due','partial') DEFAULT NULL,
  `adjustment_type` enum('normal','abnormal') DEFAULT NULL,
  `contact_id` int(11) UNSIGNED DEFAULT NULL,
  `customer_group_id` int(11) DEFAULT NULL COMMENT 'used to add customer group while selling',
  `invoice_no` varchar(191) DEFAULT NULL,
  `ref_no` varchar(191) DEFAULT NULL,
  `source` varchar(191) DEFAULT NULL,
  `subscription_no` varchar(191) DEFAULT NULL,
  `subscription_repeat_on` varchar(191) DEFAULT NULL,
  `transaction_date` datetime NOT NULL,
  `total_before_tax` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Total before the purchase/invoice tax, this includeds the indivisual product tax',
  `tax_id` int(10) UNSIGNED DEFAULT NULL,
  `tax_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `discount_type` enum('fixed','percentage') DEFAULT NULL,
  `discount_amount` decimal(22,4) DEFAULT 0.0000,
  `rp_redeemed` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `rp_redeemed_amount` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'rp is the short form of reward points',
  `shipping_details` varchar(191) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `shipping_status` varchar(191) DEFAULT NULL,
  `delivered_to` varchar(191) DEFAULT NULL,
  `shipping_charges` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `shipping_custom_field_1` varchar(191) DEFAULT NULL,
  `shipping_custom_field_2` varchar(191) DEFAULT NULL,
  `shipping_custom_field_3` varchar(191) DEFAULT NULL,
  `shipping_custom_field_4` varchar(191) DEFAULT NULL,
  `shipping_custom_field_5` varchar(191) DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `staff_note` text DEFAULT NULL,
  `is_export` tinyint(1) NOT NULL DEFAULT 0,
  `export_custom_fields_info` longtext DEFAULT NULL,
  `round_off_amount` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Difference of rounded total and actual total',
  `additional_expense_key_1` varchar(191) DEFAULT NULL,
  `additional_expense_value_1` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `additional_expense_key_2` varchar(191) DEFAULT NULL,
  `additional_expense_value_2` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `additional_expense_key_3` varchar(191) DEFAULT NULL,
  `additional_expense_value_3` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `additional_expense_key_4` varchar(191) DEFAULT NULL,
  `additional_expense_value_4` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `final_total` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `expense_category_id` int(10) UNSIGNED DEFAULT NULL,
  `expense_sub_category_id` int(11) DEFAULT NULL,
  `expense_for` int(10) UNSIGNED DEFAULT NULL,
  `commission_agent` int(11) DEFAULT NULL,
  `document` varchar(191) DEFAULT NULL,
  `is_direct_sale` tinyint(1) NOT NULL DEFAULT 0,
  `is_suspend` tinyint(1) NOT NULL DEFAULT 0,
  `exchange_rate` decimal(20,3) NOT NULL DEFAULT 1.000,
  `total_amount_recovered` decimal(22,4) DEFAULT NULL COMMENT 'Used for stock adjustment.',
  `transfer_parent_id` int(11) DEFAULT NULL,
  `return_parent_id` int(11) DEFAULT NULL,
  `opening_stock_product_id` int(11) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `woocommerce_order_id` int(11) DEFAULT NULL,
  `essentials_duration` decimal(8,2) NOT NULL,
  `essentials_duration_unit` varchar(20) DEFAULT NULL,
  `essentials_amount_per_unit_duration` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `essentials_allowances` text DEFAULT NULL,
  `essentials_deductions` text DEFAULT NULL,
  `repair_completed_on` datetime DEFAULT NULL,
  `repair_warranty_id` int(11) DEFAULT NULL,
  `repair_brand_id` int(11) DEFAULT NULL,
  `repair_status_id` int(11) DEFAULT NULL,
  `repair_model_id` int(11) DEFAULT NULL,
  `repair_job_sheet_id` int(10) UNSIGNED DEFAULT NULL,
  `repair_defects` text DEFAULT NULL,
  `repair_serial_no` varchar(191) DEFAULT NULL,
  `repair_checklist` text DEFAULT NULL,
  `repair_security_pwd` varchar(191) DEFAULT NULL,
  `repair_security_pattern` varchar(191) DEFAULT NULL,
  `repair_due_date` datetime DEFAULT NULL,
  `repair_device_id` int(11) DEFAULT NULL,
  `repair_updates_notif` tinyint(1) NOT NULL DEFAULT 0,
  `mfg_parent_production_purchase_id` int(11) DEFAULT NULL,
  `mfg_wasted_units` decimal(22,4) DEFAULT NULL,
  `mfg_production_cost` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `mfg_production_cost_type` varchar(191) DEFAULT 'percentage',
  `mfg_is_final` tinyint(1) NOT NULL DEFAULT 0,
  `crm_is_order_request` tinyint(1) NOT NULL DEFAULT 0,
  `purchase_requisition_ids` text DEFAULT NULL,
  `prefer_payment_method` varchar(191) DEFAULT NULL,
  `prefer_payment_account` int(11) DEFAULT NULL,
  `sales_order_ids` text DEFAULT NULL,
  `purchase_order_ids` text DEFAULT NULL,
  `custom_field_1` varchar(191) DEFAULT NULL,
  `custom_field_2` varchar(191) DEFAULT NULL,
  `custom_field_3` varchar(191) DEFAULT NULL,
  `custom_field_4` varchar(191) DEFAULT NULL,
  `import_batch` int(11) DEFAULT NULL,
  `import_time` datetime DEFAULT NULL,
  `types_of_service_id` int(11) DEFAULT NULL,
  `packing_charge` decimal(22,4) DEFAULT NULL,
  `packing_charge_type` enum('fixed','percent') DEFAULT NULL,
  `service_custom_field_1` text DEFAULT NULL,
  `service_custom_field_2` text DEFAULT NULL,
  `service_custom_field_3` text DEFAULT NULL,
  `service_custom_field_4` text DEFAULT NULL,
  `service_custom_field_5` text DEFAULT NULL,
  `service_custom_field_6` text DEFAULT NULL,
  `is_created_from_api` tinyint(1) NOT NULL DEFAULT 0,
  `rp_earned` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `order_addresses` text DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `recur_interval` double(22,4) DEFAULT NULL,
  `recur_interval_type` enum('days','months','years') DEFAULT NULL,
  `recur_repetitions` int(11) DEFAULT NULL,
  `recur_stopped_on` datetime DEFAULT NULL,
  `recur_parent_id` int(11) DEFAULT NULL,
  `invoice_token` varchar(191) DEFAULT NULL,
  `pay_term_number` int(11) DEFAULT NULL,
  `pay_term_type` enum('days','months') DEFAULT NULL,
  `pjt_project_id` int(10) UNSIGNED DEFAULT NULL,
  `pjt_title` varchar(191) DEFAULT NULL,
  `selling_price_group_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_payments`
--

CREATE TABLE `transaction_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(11) UNSIGNED DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL,
  `is_return` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Used during sales to return the change',
  `amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `method` varchar(191) DEFAULT NULL,
  `payment_type` varchar(191) DEFAULT NULL,
  `transaction_no` varchar(191) DEFAULT NULL,
  `card_transaction_number` varchar(191) DEFAULT NULL,
  `card_number` varchar(191) DEFAULT NULL,
  `card_type` varchar(191) DEFAULT NULL,
  `card_holder_name` varchar(191) DEFAULT NULL,
  `card_month` varchar(191) DEFAULT NULL,
  `card_year` varchar(191) DEFAULT NULL,
  `card_security` varchar(5) DEFAULT NULL,
  `cheque_number` varchar(191) DEFAULT NULL,
  `bank_account_number` varchar(191) DEFAULT NULL,
  `paid_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `paid_through_link` tinyint(1) NOT NULL DEFAULT 0,
  `gateway` varchar(191) DEFAULT NULL,
  `is_advance` tinyint(1) NOT NULL DEFAULT 0,
  `payment_for` int(11) DEFAULT NULL COMMENT 'stores the contact id',
  `parent_id` int(11) DEFAULT NULL,
  `note` varchar(191) DEFAULT NULL,
  `document` varchar(191) DEFAULT NULL,
  `payment_ref_no` varchar(191) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_sell_lines`
--

CREATE TABLE `transaction_sell_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `mfg_waste_percent` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `mfg_ingredient_group_id` int(11) DEFAULT NULL,
  `secondary_unit_quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `quantity_returned` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `unit_price_before_discount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `unit_price` decimal(22,4) DEFAULT NULL COMMENT 'Sell price excluding tax',
  `line_discount_type` enum('fixed','percentage') DEFAULT NULL,
  `line_discount_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `unit_price_inc_tax` decimal(22,4) DEFAULT NULL COMMENT 'Sell price including tax',
  `item_tax` decimal(22,4) NOT NULL COMMENT 'Tax for one quantity',
  `tax_id` int(10) UNSIGNED DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `lot_no_line_id` int(11) DEFAULT NULL,
  `sell_line_note` text DEFAULT NULL,
  `woocommerce_line_items_id` int(11) DEFAULT NULL,
  `so_line_id` int(11) DEFAULT NULL,
  `so_quantity_invoiced` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `res_service_staff_id` int(11) DEFAULT NULL,
  `res_line_order_status` varchar(191) DEFAULT NULL,
  `parent_sell_line_id` int(11) DEFAULT NULL,
  `children_type` varchar(191) NOT NULL DEFAULT '' COMMENT 'Type of children for the parent, like modifier or combo',
  `sub_unit_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_sell_lines_purchase_lines`
--

CREATE TABLE `transaction_sell_lines_purchase_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `sell_line_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'id from transaction_sell_lines',
  `stock_adjustment_line_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'id from stock_adjustment_lines',
  `purchase_line_id` int(10) UNSIGNED NOT NULL COMMENT 'id from purchase_lines',
  `quantity` decimal(22,4) NOT NULL,
  `qty_returned` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transfers`
--

CREATE TABLE `transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_transaction_number` varchar(191) NOT NULL,
  `transfer_from_id` bigint(20) UNSIGNED NOT NULL,
  `transfer_to_id` bigint(20) UNSIGNED NOT NULL,
  `transfer_by_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `types_of_services`
--

CREATE TABLE `types_of_services` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `location_price_group` text DEFAULT NULL,
  `packing_charge` decimal(22,4) DEFAULT NULL,
  `packing_charge_type` enum('fixed','percent') DEFAULT NULL,
  `enable_custom_fields` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `actual_name` varchar(191) NOT NULL,
  `short_name` varchar(191) NOT NULL,
  `allow_decimal` tinyint(1) NOT NULL,
  `base_unit_id` int(11) DEFAULT NULL,
  `base_unit_multiplier` decimal(20,4) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `business_id`, `actual_name`, `short_name`, `allow_decimal`, `base_unit_id`, `base_unit_multiplier`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Pieces', 'Pc(s)', 0, NULL, NULL, 1, NULL, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_type` varchar(191) NOT NULL DEFAULT 'user',
  `surname` char(10) DEFAULT NULL,
  `first_name` varchar(191) NOT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `username` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `password` varchar(191) DEFAULT NULL,
  `language` char(7) NOT NULL DEFAULT 'en',
  `contact_no` char(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `business_id` int(10) UNSIGNED DEFAULT NULL,
  `essentials_department_id` int(11) DEFAULT NULL,
  `essentials_designation_id` int(11) DEFAULT NULL,
  `essentials_salary` decimal(22,4) DEFAULT NULL,
  `essentials_pay_period` varchar(191) DEFAULT NULL,
  `essentials_pay_cycle` varchar(191) DEFAULT NULL,
  `available_at` datetime DEFAULT NULL COMMENT 'Service staff avilable at. Calculated from product preparation_time_in_minutes',
  `paused_at` datetime DEFAULT NULL COMMENT 'Service staff available time paused at, Will be nulled on resume.',
  `max_sales_discount_percent` decimal(5,2) DEFAULT NULL,
  `allow_login` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','inactive','terminated') NOT NULL DEFAULT 'active',
  `crm_contact_id` int(10) UNSIGNED DEFAULT NULL,
  `is_cmmsn_agnt` tinyint(1) NOT NULL DEFAULT 0,
  `cmmsn_percent` decimal(4,2) NOT NULL DEFAULT 0.00,
  `selected_contacts` tinyint(1) NOT NULL DEFAULT 0,
  `dob` date DEFAULT NULL,
  `gender` varchar(191) DEFAULT NULL,
  `marital_status` enum('married','unmarried','divorced') DEFAULT NULL,
  `blood_group` char(10) DEFAULT NULL,
  `contact_number` char(20) DEFAULT NULL,
  `alt_number` varchar(191) DEFAULT NULL,
  `family_number` varchar(191) DEFAULT NULL,
  `fb_link` varchar(191) DEFAULT NULL,
  `twitter_link` varchar(191) DEFAULT NULL,
  `social_media_1` varchar(191) DEFAULT NULL,
  `social_media_2` varchar(191) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `guardian_name` varchar(191) DEFAULT NULL,
  `custom_field_1` varchar(191) DEFAULT NULL,
  `custom_field_2` varchar(191) DEFAULT NULL,
  `custom_field_3` varchar(191) DEFAULT NULL,
  `custom_field_4` varchar(191) DEFAULT NULL,
  `bank_details` longtext DEFAULT NULL,
  `id_proof_name` varchar(191) DEFAULT NULL,
  `id_proof_number` varchar(191) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL COMMENT 'user primary work location',
  `crm_department` varchar(191) DEFAULT NULL COMMENT 'Contact person''s department',
  `crm_designation` varchar(191) DEFAULT NULL COMMENT 'Contact person''s designation',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_type`, `surname`, `first_name`, `last_name`, `username`, `email`, `password`, `language`, `contact_no`, `address`, `remember_token`, `business_id`, `essentials_department_id`, `essentials_designation_id`, `essentials_salary`, `essentials_pay_period`, `essentials_pay_cycle`, `available_at`, `paused_at`, `max_sales_discount_percent`, `allow_login`, `status`, `crm_contact_id`, `is_cmmsn_agnt`, `cmmsn_percent`, `selected_contacts`, `dob`, `gender`, `marital_status`, `blood_group`, `contact_number`, `alt_number`, `family_number`, `fb_link`, `twitter_link`, `social_media_1`, `social_media_2`, `permanent_address`, `current_address`, `guardian_name`, `custom_field_1`, `custom_field_2`, `custom_field_3`, `custom_field_4`, `bank_details`, `id_proof_name`, `id_proof_number`, `location_id`, `crm_department`, `crm_designation`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'user', NULL, 'Nacional', 'Code', 'admin@nacionalcode.com', 'admin@nacionalcode.com', '$2y$10$XYeatZ03o83zdFWT05FQSu3V6AY.gDYuOsvSZxaypqxABRZJEfXXm', 'es', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'active', NULL, 0, '0.00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-06-17 01:33:40', '2023-06-17 01:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_contact_access`
--

CREATE TABLE `user_contact_access` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variations`
--

CREATE TABLE `variations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `sub_sku` varchar(191) DEFAULT NULL,
  `product_variation_id` int(10) UNSIGNED NOT NULL,
  `woocommerce_variation_id` int(11) DEFAULT NULL,
  `variation_value_id` int(11) DEFAULT NULL,
  `default_purchase_price` decimal(22,4) DEFAULT NULL,
  `dpp_inc_tax` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `profit_percent` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `default_sell_price` decimal(22,4) DEFAULT NULL,
  `sell_price_inc_tax` decimal(22,4) DEFAULT NULL COMMENT 'Sell price including tax',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `combo_variations` text DEFAULT NULL COMMENT 'Contains the combo variation details'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variation_group_prices`
--

CREATE TABLE `variation_group_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(10) UNSIGNED NOT NULL,
  `price_group_id` int(10) UNSIGNED NOT NULL,
  `price_inc_tax` decimal(22,4) NOT NULL,
  `price_type` varchar(191) NOT NULL DEFAULT 'fixed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variation_location_details`
--

CREATE TABLE `variation_location_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_variation_id` int(10) UNSIGNED NOT NULL COMMENT 'id from product_variations table',
  `variation_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `qty_available` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variation_templates`
--

CREATE TABLE `variation_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `woocommerce_attr_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variation_value_templates`
--

CREATE TABLE `variation_value_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `variation_template_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warranties`
--

CREATE TABLE `warranties` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `duration_type` enum('days','months','years') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `woocommerce_sync_logs`
--

CREATE TABLE `woocommerce_sync_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `sync_type` varchar(191) NOT NULL,
  `operation_type` varchar(191) DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `details` longtext DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounts_business_id_index` (`business_id`),
  ADD KEY `accounts_account_type_id_index` (`account_type_id`),
  ADD KEY `accounts_created_by_index` (`created_by`);

--
-- Indexes for table `account_detail_types`
--
ALTER TABLE `account_detail_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_subtypes`
--
ALTER TABLE `account_subtypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_transactions_account_id_index` (`account_id`),
  ADD KEY `account_transactions_transaction_id_index` (`transaction_id`),
  ADD KEY `account_transactions_transaction_payment_id_index` (`transaction_payment_id`),
  ADD KEY `account_transactions_transfer_transaction_id_index` (`transfer_transaction_id`),
  ADD KEY `account_transactions_created_by_index` (`created_by`),
  ADD KEY `account_transactions_type_index` (`type`),
  ADD KEY `account_transactions_sub_type_index` (`sub_type`),
  ADD KEY `account_transactions_operation_date_index` (`operation_date`);

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_types_parent_account_type_id_index` (`parent_account_type_id`),
  ADD KEY `account_types_business_id_index` (`business_id`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `aiassistance_history`
--
ALTER TABLE `aiassistance_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assets_business_id_foreign` (`business_id`),
  ADD KEY `assets_category_id_foreign` (`category_id`),
  ADD KEY `assets_created_by_foreign` (`created_by`);

--
-- Indexes for table `asset_maintenances`
--
ALTER TABLE `asset_maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_maintenances_business_id_index` (`business_id`),
  ADD KEY `asset_maintenances_asset_id_index` (`asset_id`),
  ADD KEY `asset_maintenances_status_index` (`status`),
  ADD KEY `asset_maintenances_priority_index` (`priority`),
  ADD KEY `asset_maintenances_created_by_index` (`created_by`),
  ADD KEY `asset_maintenances_assigned_to_index` (`assigned_to`);

--
-- Indexes for table `asset_transactions`
--
ALTER TABLE `asset_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_transactions_business_id_foreign` (`business_id`),
  ADD KEY `asset_transactions_asset_id_foreign` (`asset_id`),
  ADD KEY `asset_transactions_receiver_foreign` (`receiver`),
  ADD KEY `asset_transactions_parent_id_foreign` (`parent_id`),
  ADD KEY `asset_transactions_created_by_foreign` (`created_by`);

--
-- Indexes for table `asset_warranties`
--
ALTER TABLE `asset_warranties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barcodes`
--
ALTER TABLE `barcodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barcodes_business_id_foreign` (`business_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_contact_id_foreign` (`contact_id`),
  ADD KEY `bookings_business_id_foreign` (`business_id`),
  ADD KEY `bookings_created_by_foreign` (`created_by`),
  ADD KEY `bookings_table_id_index` (`table_id`),
  ADD KEY `bookings_waiter_id_index` (`waiter_id`),
  ADD KEY `bookings_location_id_index` (`location_id`),
  ADD KEY `bookings_booking_status_index` (`booking_status`),
  ADD KEY `bookings_correspondent_id_index` (`correspondent_id`);

--
-- Indexes for table `branch_capital`
--
ALTER TABLE `branch_capital`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brands_business_id_foreign` (`business_id`),
  ADD KEY `brands_created_by_foreign` (`created_by`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_owner_id_foreign` (`owner_id`),
  ADD KEY `business_currency_id_foreign` (`currency_id`),
  ADD KEY `business_default_sales_tax_foreign` (`default_sales_tax`);

--
-- Indexes for table `business_locations`
--
ALTER TABLE `business_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_locations_business_id_index` (`business_id`),
  ADD KEY `business_locations_invoice_scheme_id_foreign` (`invoice_scheme_id`),
  ADD KEY `business_locations_invoice_layout_id_foreign` (`invoice_layout_id`),
  ADD KEY `business_locations_sale_invoice_layout_id_index` (`sale_invoice_layout_id`),
  ADD KEY `business_locations_selling_price_group_id_index` (`selling_price_group_id`),
  ADD KEY `business_locations_receipt_printer_type_index` (`receipt_printer_type`),
  ADD KEY `business_locations_printer_id_index` (`printer_id`);

--
-- Indexes for table `cash_denominations`
--
ALTER TABLE `cash_denominations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_denominations_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_registers_business_id_foreign` (`business_id`),
  ADD KEY `cash_registers_user_id_foreign` (`user_id`),
  ADD KEY `cash_registers_location_id_index` (`location_id`);

--
-- Indexes for table `cash_register_transactions`
--
ALTER TABLE `cash_register_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_register_transactions_cash_register_id_foreign` (`cash_register_id`),
  ADD KEY `cash_register_transactions_transaction_id_index` (`transaction_id`),
  ADD KEY `cash_register_transactions_type_index` (`type`),
  ADD KEY `cash_register_transactions_transaction_type_index` (`transaction_type`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_business_id_foreign` (`business_id`),
  ADD KEY `categories_created_by_foreign` (`created_by`),
  ADD KEY `categories_parent_id_index` (`parent_id`),
  ADD KEY `categories_woocommerce_cat_id_index` (`woocommerce_cat_id`);

--
-- Indexes for table `categorizables`
--
ALTER TABLE `categorizables`
  ADD KEY `categorizables_categorizable_type_categorizable_id_index` (`categorizable_type`,`categorizable_id`);

--
-- Indexes for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cms_pages`
--
ALTER TABLE `cms_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cms_page_metas`
--
ALTER TABLE `cms_page_metas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cms_page_metas_cms_page_id_foreign` (`cms_page_id`);

--
-- Indexes for table `cms_site_details`
--
ALTER TABLE `cms_site_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contacts_business_id_foreign` (`business_id`),
  ADD KEY `contacts_created_by_foreign` (`created_by`),
  ADD KEY `contacts_type_index` (`type`),
  ADD KEY `contacts_contact_status_index` (`contact_status`),
  ADD KEY `contacts_crm_source_index` (`crm_source`),
  ADD KEY `contacts_crm_life_stage_index` (`crm_life_stage`),
  ADD KEY `contacts_converted_by_index` (`converted_by`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_call_logs`
--
ALTER TABLE `crm_call_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_call_logs_business_id_index` (`business_id`),
  ADD KEY `crm_call_logs_user_id_index` (`user_id`),
  ADD KEY `crm_call_logs_contact_id_index` (`contact_id`),
  ADD KEY `crm_call_logs_created_by_index` (`created_by`);

--
-- Indexes for table `crm_campaigns`
--
ALTER TABLE `crm_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_campaigns_business_id_foreign` (`business_id`),
  ADD KEY `crm_campaigns_created_by_index` (`created_by`);

--
-- Indexes for table `crm_contact_person_commissions`
--
ALTER TABLE `crm_contact_person_commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_lead_users`
--
ALTER TABLE `crm_lead_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_lead_users_user_id_index` (`user_id`),
  ADD KEY `crm_lead_users_contact_id_index` (`contact_id`);

--
-- Indexes for table `crm_marketplaces`
--
ALTER TABLE `crm_marketplaces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crm_proposals`
--
ALTER TABLE `crm_proposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_proposals_business_id_foreign` (`business_id`),
  ADD KEY `crm_proposals_contact_id_foreign` (`contact_id`),
  ADD KEY `crm_proposals_sent_by_index` (`sent_by`);

--
-- Indexes for table `crm_proposal_templates`
--
ALTER TABLE `crm_proposal_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_proposal_templates_business_id_foreign` (`business_id`),
  ADD KEY `crm_proposal_templates_created_by_index` (`created_by`);

--
-- Indexes for table `crm_schedules`
--
ALTER TABLE `crm_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_schedules_created_by_index` (`created_by`),
  ADD KEY `crm_schedules_business_id_index` (`business_id`),
  ADD KEY `crm_schedules_contact_id_index` (`contact_id`),
  ADD KEY `crm_schedules_schedule_type_index` (`schedule_type`),
  ADD KEY `crm_schedules_notify_type_index` (`notify_type`);

--
-- Indexes for table `crm_schedule_logs`
--
ALTER TABLE `crm_schedule_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_schedule_logs_schedule_id_foreign` (`schedule_id`),
  ADD KEY `crm_schedule_logs_created_by_index` (`created_by`);

--
-- Indexes for table `crm_schedule_users`
--
ALTER TABLE `crm_schedule_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_schedule_users_schedule_id_foreign` (`schedule_id`),
  ADD KEY `crm_schedule_users_user_id_index` (`user_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_groups`
--
ALTER TABLE `customer_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_groups_business_id_foreign` (`business_id`),
  ADD KEY `customer_groups_created_by_index` (`created_by`),
  ADD KEY `customer_groups_price_calculation_type_index` (`price_calculation_type`),
  ADD KEY `customer_groups_selling_price_group_id_index` (`selling_price_group_id`);

--
-- Indexes for table `dashboard_configurations`
--
ALTER TABLE `dashboard_configurations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dashboard_configurations_business_id_foreign` (`business_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discounts_business_id_index` (`business_id`),
  ADD KEY `discounts_brand_id_index` (`brand_id`),
  ADD KEY `discounts_category_id_index` (`category_id`),
  ADD KEY `discounts_location_id_index` (`location_id`),
  ADD KEY `discounts_priority_index` (`priority`),
  ADD KEY `discounts_spg_index` (`spg`);

--
-- Indexes for table `discount_variations`
--
ALTER TABLE `discount_variations`
  ADD KEY `discount_variations_discount_id_index` (`discount_id`),
  ADD KEY `discount_variations_variation_id_index` (`variation_id`);

--
-- Indexes for table `document_and_notes`
--
ALTER TABLE `document_and_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_and_notes_business_id_index` (`business_id`),
  ADD KEY `document_and_notes_notable_id_index` (`notable_id`),
  ADD KEY `document_and_notes_created_by_index` (`created_by`);

--
-- Indexes for table `essentials_allowances_and_deductions`
--
ALTER TABLE `essentials_allowances_and_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_allowances_and_deductions_business_id_index` (`business_id`);

--
-- Indexes for table `essentials_attendances`
--
ALTER TABLE `essentials_attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_attendances_user_id_index` (`user_id`),
  ADD KEY `essentials_attendances_business_id_index` (`business_id`),
  ADD KEY `essentials_attendances_essentials_shift_id_index` (`essentials_shift_id`);

--
-- Indexes for table `essentials_documents`
--
ALTER TABLE `essentials_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `essentials_document_shares`
--
ALTER TABLE `essentials_document_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_document_shares_document_id_index` (`document_id`),
  ADD KEY `essentials_document_shares_value_type_index` (`value_type`);

--
-- Indexes for table `essentials_holidays`
--
ALTER TABLE `essentials_holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_holidays_business_id_index` (`business_id`),
  ADD KEY `essentials_holidays_location_id_index` (`location_id`);

--
-- Indexes for table `essentials_kb`
--
ALTER TABLE `essentials_kb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_kb_business_id_index` (`business_id`),
  ADD KEY `essentials_kb_parent_id_index` (`parent_id`),
  ADD KEY `essentials_kb_created_by_index` (`created_by`);

--
-- Indexes for table `essentials_kb_users`
--
ALTER TABLE `essentials_kb_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_kb_users_kb_id_index` (`kb_id`),
  ADD KEY `essentials_kb_users_user_id_index` (`user_id`);

--
-- Indexes for table `essentials_leaves`
--
ALTER TABLE `essentials_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_leaves_essentials_leave_type_id_index` (`essentials_leave_type_id`),
  ADD KEY `essentials_leaves_business_id_index` (`business_id`),
  ADD KEY `essentials_leaves_user_id_index` (`user_id`);

--
-- Indexes for table `essentials_leave_types`
--
ALTER TABLE `essentials_leave_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_leave_types_business_id_index` (`business_id`);

--
-- Indexes for table `essentials_messages`
--
ALTER TABLE `essentials_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_messages_business_id_index` (`business_id`),
  ADD KEY `essentials_messages_user_id_index` (`user_id`),
  ADD KEY `essentials_messages_location_id_index` (`location_id`);

--
-- Indexes for table `essentials_payroll_groups`
--
ALTER TABLE `essentials_payroll_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `essentials_payroll_group_transactions`
--
ALTER TABLE `essentials_payroll_group_transactions`
  ADD KEY `essentials_payroll_group_transactions_payroll_group_id_foreign` (`payroll_group_id`);

--
-- Indexes for table `essentials_reminders`
--
ALTER TABLE `essentials_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_reminders_business_id_index` (`business_id`),
  ADD KEY `essentials_reminders_user_id_index` (`user_id`);

--
-- Indexes for table `essentials_shifts`
--
ALTER TABLE `essentials_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_shifts_type_index` (`type`),
  ADD KEY `essentials_shifts_business_id_index` (`business_id`);

--
-- Indexes for table `essentials_todo_comments`
--
ALTER TABLE `essentials_todo_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_todo_comments_task_id_index` (`task_id`),
  ADD KEY `essentials_todo_comments_comment_by_index` (`comment_by`);

--
-- Indexes for table `essentials_to_dos`
--
ALTER TABLE `essentials_to_dos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_to_dos_status_index` (`status`),
  ADD KEY `essentials_to_dos_priority_index` (`priority`),
  ADD KEY `essentials_to_dos_created_by_index` (`created_by`),
  ADD KEY `essentials_to_dos_business_id_index` (`business_id`),
  ADD KEY `essentials_to_dos_task_id_index` (`task_id`);

--
-- Indexes for table `essentials_user_allowance_and_deductions`
--
ALTER TABLE `essentials_user_allowance_and_deductions`
  ADD KEY `essentials_user_allowance_and_deductions_user_id_index` (`user_id`),
  ADD KEY `allow_deduct_index` (`allowance_deduction_id`);

--
-- Indexes for table `essentials_user_sales_targets`
--
ALTER TABLE `essentials_user_sales_targets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `essentials_user_shifts`
--
ALTER TABLE `essentials_user_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `essentials_user_shifts_user_id_index` (`user_id`),
  ADD KEY `essentials_user_shifts_essentials_shift_id_index` (`essentials_shift_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_categories_business_id_foreign` (`business_id`);

--
-- Indexes for table `group_sub_taxes`
--
ALTER TABLE `group_sub_taxes`
  ADD KEY `group_sub_taxes_group_tax_id_foreign` (`group_tax_id`),
  ADD KEY `group_sub_taxes_tax_id_foreign` (`tax_id`);

--
-- Indexes for table `invoice_layouts`
--
ALTER TABLE `invoice_layouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_layouts_business_id_foreign` (`business_id`);

--
-- Indexes for table `invoice_schemes`
--
ALTER TABLE `invoice_schemes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_schemes_business_id_foreign` (`business_id`),
  ADD KEY `invoice_schemes_scheme_type_index` (`scheme_type`),
  ADD KEY `invoice_schemes_number_type_index` (`number_type`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chart_of_account_id_index` (`chart_of_account_id`),
  ADD KEY `currency_id_index` (`currency_id`),
  ADD KEY `created_by_id_index` (`created_by_id`),
  ADD KEY `journal_entries_contact_id_index` (`contact_id`),
  ADD KEY `journal_entries_location_id_index` (`location_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_business_id_index` (`business_id`),
  ADD KEY `media_uploaded_by_index` (`uploaded_by`),
  ADD KEY `media_woocommerce_media_id_index` (`woocommerce_media_id`);

--
-- Indexes for table `mfg_ingredient_groups`
--
ALTER TABLE `mfg_ingredient_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mfg_recipes`
--
ALTER TABLE `mfg_recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mfg_recipes_product_id_index` (`product_id`),
  ADD KEY `mfg_recipes_variation_id_index` (`variation_id`);

--
-- Indexes for table `mfg_recipe_ingredients`
--
ALTER TABLE `mfg_recipe_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mfg_recipe_ingredients_mfg_recipe_id_index` (`mfg_recipe_id`),
  ADD KEY `mfg_recipe_ingredients_variation_id_index` (`variation_id`),
  ADD KEY `mfg_recipe_ingredients_sub_unit_id_index` (`sub_unit_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_personal_access_clients_client_id_index` (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_types`
--
ALTER TABLE `payment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pjt_invoice_lines`
--
ALTER TABLE `pjt_invoice_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pjt_invoice_lines_transaction_id_foreign` (`transaction_id`),
  ADD KEY `pjt_invoice_lines_tax_rate_id_index` (`tax_rate_id`);

--
-- Indexes for table `pjt_projects`
--
ALTER TABLE `pjt_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pjt_projects_business_id_index` (`business_id`),
  ADD KEY `pjt_projects_contact_id_index` (`contact_id`),
  ADD KEY `pjt_projects_lead_id_index` (`lead_id`),
  ADD KEY `pjt_projects_created_by_index` (`created_by`);

--
-- Indexes for table `pjt_project_members`
--
ALTER TABLE `pjt_project_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pjt_project_members_project_id_foreign` (`project_id`),
  ADD KEY `pjt_project_members_user_id_index` (`user_id`);

--
-- Indexes for table `pjt_project_tasks`
--
ALTER TABLE `pjt_project_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pjt_project_tasks_project_id_foreign` (`project_id`),
  ADD KEY `pjt_project_tasks_business_id_index` (`business_id`),
  ADD KEY `pjt_project_tasks_created_by_index` (`created_by`);

--
-- Indexes for table `pjt_project_task_comments`
--
ALTER TABLE `pjt_project_task_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pjt_project_task_comments_project_task_id_foreign` (`project_task_id`);

--
-- Indexes for table `pjt_project_task_members`
--
ALTER TABLE `pjt_project_task_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pjt_project_task_members_project_task_id_foreign` (`project_task_id`),
  ADD KEY `pjt_project_task_members_user_id_index` (`user_id`);

--
-- Indexes for table `pjt_project_time_logs`
--
ALTER TABLE `pjt_project_time_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pjt_project_time_logs_project_id_foreign` (`project_id`),
  ADD KEY `pjt_project_time_logs_project_task_id_foreign` (`project_task_id`),
  ADD KEY `pjt_project_time_logs_user_id_index` (`user_id`),
  ADD KEY `pjt_project_time_logs_created_by_index` (`created_by`);

--
-- Indexes for table `printers`
--
ALTER TABLE `printers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `printers_business_id_foreign` (`business_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_sub_category_id_foreign` (`sub_category_id`),
  ADD KEY `products_tax_foreign` (`tax`),
  ADD KEY `products_name_index` (`name`),
  ADD KEY `products_business_id_index` (`business_id`),
  ADD KEY `products_unit_id_index` (`unit_id`),
  ADD KEY `products_created_by_index` (`created_by`),
  ADD KEY `products_warranty_id_index` (`warranty_id`),
  ADD KEY `products_type_index` (`type`),
  ADD KEY `products_tax_type_index` (`tax_type`),
  ADD KEY `products_barcode_type_index` (`barcode_type`),
  ADD KEY `products_secondary_unit_id_index` (`secondary_unit_id`),
  ADD KEY `products_repair_model_id_index` (`repair_model_id`),
  ADD KEY `products_woocommerce_product_id_index` (`woocommerce_product_id`),
  ADD KEY `products_woocommerce_media_id_index` (`woocommerce_media_id`);

--
-- Indexes for table `product_locations`
--
ALTER TABLE `product_locations`
  ADD KEY `product_locations_product_id_index` (`product_id`),
  ADD KEY `product_locations_location_id_index` (`location_id`);

--
-- Indexes for table `product_racks`
--
ALTER TABLE `product_racks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_racks_business_id_index` (`business_id`),
  ADD KEY `product_racks_location_id_index` (`location_id`),
  ADD KEY `product_racks_product_id_index` (`product_id`);

--
-- Indexes for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variations_name_index` (`name`),
  ADD KEY `product_variations_product_id_index` (`product_id`);

--
-- Indexes for table `purchase_lines`
--
ALTER TABLE `purchase_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_lines_transaction_id_foreign` (`transaction_id`),
  ADD KEY `purchase_lines_product_id_foreign` (`product_id`),
  ADD KEY `purchase_lines_variation_id_foreign` (`variation_id`),
  ADD KEY `purchase_lines_tax_id_foreign` (`tax_id`),
  ADD KEY `purchase_lines_sub_unit_id_index` (`sub_unit_id`),
  ADD KEY `purchase_lines_lot_number_index` (`lot_number`);

--
-- Indexes for table `reference_counts`
--
ALTER TABLE `reference_counts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reference_counts_business_id_index` (`business_id`);

--
-- Indexes for table `repair_device_models`
--
ALTER TABLE `repair_device_models`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_device_models_business_id_index` (`business_id`),
  ADD KEY `repair_device_models_brand_id_index` (`brand_id`),
  ADD KEY `repair_device_models_device_id_index` (`device_id`),
  ADD KEY `repair_device_models_created_by_index` (`created_by`);

--
-- Indexes for table `repair_job_sheets`
--
ALTER TABLE `repair_job_sheets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_job_sheets_business_id_index` (`business_id`),
  ADD KEY `repair_job_sheets_location_id_index` (`location_id`),
  ADD KEY `repair_job_sheets_contact_id_index` (`contact_id`),
  ADD KEY `repair_job_sheets_brand_id_index` (`brand_id`),
  ADD KEY `repair_job_sheets_device_id_index` (`device_id`),
  ADD KEY `repair_job_sheets_device_model_id_index` (`device_model_id`),
  ADD KEY `repair_job_sheets_status_id_index` (`status_id`),
  ADD KEY `repair_job_sheets_service_staff_index` (`service_staff`),
  ADD KEY `repair_job_sheets_created_by_index` (`created_by`);

--
-- Indexes for table `repair_statuses`
--
ALTER TABLE `repair_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `res_product_modifier_sets`
--
ALTER TABLE `res_product_modifier_sets`
  ADD KEY `res_product_modifier_sets_modifier_set_id_foreign` (`modifier_set_id`);

--
-- Indexes for table `res_tables`
--
ALTER TABLE `res_tables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `res_tables_business_id_foreign` (`business_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `roles_business_id_foreign` (`business_id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `selling_price_groups`
--
ALTER TABLE `selling_price_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `selling_price_groups_business_id_foreign` (`business_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD UNIQUE KEY `sessions_id_unique` (`id`);

--
-- Indexes for table `sheet_spreadsheets`
--
ALTER TABLE `sheet_spreadsheets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sheet_spreadsheets_business_id_foreign` (`business_id`),
  ADD KEY `sheet_spreadsheets_created_by_index` (`created_by`);

--
-- Indexes for table `sheet_spreadsheet_shares`
--
ALTER TABLE `sheet_spreadsheet_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sheet_spreadsheet_shares_sheet_spreadsheet_id_foreign` (`sheet_spreadsheet_id`),
  ADD KEY `sheet_spreadsheet_shares_shared_with_index` (`shared_with`),
  ADD KEY `sheet_spreadsheet_shares_shared_id_index` (`shared_id`);

--
-- Indexes for table `stock_adjustment_lines`
--
ALTER TABLE `stock_adjustment_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_adjustment_lines_product_id_foreign` (`product_id`),
  ADD KEY `stock_adjustment_lines_variation_id_foreign` (`variation_id`),
  ADD KEY `stock_adjustment_lines_transaction_id_index` (`transaction_id`),
  ADD KEY `stock_adjustment_lines_lot_no_line_id_index` (`lot_no_line_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_business_id_foreign` (`business_id`),
  ADD KEY `subscriptions_package_id_index` (`package_id`),
  ADD KEY `subscriptions_created_id_index` (`created_id`);

--
-- Indexes for table `superadmin_communicator_logs`
--
ALTER TABLE `superadmin_communicator_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `superadmin_frontend_pages`
--
ALTER TABLE `superadmin_frontend_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_rates`
--
ALTER TABLE `tax_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tax_rates_business_id_foreign` (`business_id`),
  ADD KEY `tax_rates_created_by_foreign` (`created_by`),
  ADD KEY `tax_rates_woocommerce_tax_rate_id_index` (`woocommerce_tax_rate_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_tax_id_foreign` (`tax_id`),
  ADD KEY `transactions_business_id_index` (`business_id`),
  ADD KEY `transactions_type_index` (`type`),
  ADD KEY `transactions_contact_id_index` (`contact_id`),
  ADD KEY `transactions_transaction_date_index` (`transaction_date`),
  ADD KEY `transactions_created_by_index` (`created_by`),
  ADD KEY `transactions_location_id_index` (`location_id`),
  ADD KEY `transactions_expense_for_foreign` (`expense_for`),
  ADD KEY `transactions_expense_category_id_index` (`expense_category_id`),
  ADD KEY `transactions_sub_type_index` (`sub_type`),
  ADD KEY `transactions_return_parent_id_index` (`return_parent_id`),
  ADD KEY `type` (`type`),
  ADD KEY `transactions_status_index` (`status`),
  ADD KEY `transactions_sub_status_index` (`sub_status`),
  ADD KEY `transactions_res_table_id_index` (`res_table_id`),
  ADD KEY `transactions_res_waiter_id_index` (`res_waiter_id`),
  ADD KEY `transactions_res_order_status_index` (`res_order_status`),
  ADD KEY `transactions_payment_status_index` (`payment_status`),
  ADD KEY `transactions_discount_type_index` (`discount_type`),
  ADD KEY `transactions_commission_agent_index` (`commission_agent`),
  ADD KEY `transactions_transfer_parent_id_index` (`transfer_parent_id`),
  ADD KEY `transactions_types_of_service_id_index` (`types_of_service_id`),
  ADD KEY `transactions_packing_charge_type_index` (`packing_charge_type`),
  ADD KEY `transactions_recur_parent_id_index` (`recur_parent_id`),
  ADD KEY `transactions_selling_price_group_id_index` (`selling_price_group_id`),
  ADD KEY `transactions_delivery_date_index` (`delivery_date`),
  ADD KEY `transactions_mfg_parent_production_purchase_id_index` (`mfg_parent_production_purchase_id`),
  ADD KEY `transactions_repair_model_id_index` (`repair_model_id`),
  ADD KEY `transactions_repair_warranty_id_index` (`repair_warranty_id`),
  ADD KEY `transactions_repair_brand_id_index` (`repair_brand_id`),
  ADD KEY `transactions_repair_status_id_index` (`repair_status_id`),
  ADD KEY `transactions_repair_device_id_index` (`repair_device_id`),
  ADD KEY `transactions_repair_job_sheet_id_index` (`repair_job_sheet_id`),
  ADD KEY `transactions_pjt_project_id_foreign` (`pjt_project_id`),
  ADD KEY `transactions_woocommerce_order_id_index` (`woocommerce_order_id`);

--
-- Indexes for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_payments_transaction_id_foreign` (`transaction_id`),
  ADD KEY `transaction_payments_created_by_index` (`created_by`),
  ADD KEY `transaction_payments_parent_id_index` (`parent_id`),
  ADD KEY `transaction_payments_payment_type_index` (`payment_type`);

--
-- Indexes for table `transaction_sell_lines`
--
ALTER TABLE `transaction_sell_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_sell_lines_transaction_id_foreign` (`transaction_id`),
  ADD KEY `transaction_sell_lines_product_id_foreign` (`product_id`),
  ADD KEY `transaction_sell_lines_variation_id_foreign` (`variation_id`),
  ADD KEY `transaction_sell_lines_tax_id_foreign` (`tax_id`),
  ADD KEY `transaction_sell_lines_children_type_index` (`children_type`),
  ADD KEY `transaction_sell_lines_parent_sell_line_id_index` (`parent_sell_line_id`),
  ADD KEY `transaction_sell_lines_line_discount_type_index` (`line_discount_type`),
  ADD KEY `transaction_sell_lines_discount_id_index` (`discount_id`),
  ADD KEY `transaction_sell_lines_lot_no_line_id_index` (`lot_no_line_id`),
  ADD KEY `transaction_sell_lines_sub_unit_id_index` (`sub_unit_id`),
  ADD KEY `transaction_sell_lines_woocommerce_line_items_id_index` (`woocommerce_line_items_id`);

--
-- Indexes for table `transaction_sell_lines_purchase_lines`
--
ALTER TABLE `transaction_sell_lines_purchase_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sell_line_id` (`sell_line_id`),
  ADD KEY `stock_adjustment_line_id` (`stock_adjustment_line_id`),
  ADD KEY `purchase_line_id` (`purchase_line_id`);

--
-- Indexes for table `transfers`
--
ALTER TABLE `transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `types_of_services`
--
ALTER TABLE `types_of_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `types_of_services_business_id_index` (`business_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `units_business_id_foreign` (`business_id`),
  ADD KEY `units_created_by_foreign` (`created_by`),
  ADD KEY `units_base_unit_id_index` (`base_unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_business_id_foreign` (`business_id`),
  ADD KEY `users_user_type_index` (`user_type`),
  ADD KEY `users_crm_contact_id_index` (`crm_contact_id`),
  ADD KEY `users_essentials_department_id_index` (`essentials_department_id`),
  ADD KEY `users_essentials_designation_id_index` (`essentials_designation_id`);

--
-- Indexes for table `user_contact_access`
--
ALTER TABLE `user_contact_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_contact_access_user_id_index` (`user_id`),
  ADD KEY `user_contact_access_contact_id_index` (`contact_id`);

--
-- Indexes for table `variations`
--
ALTER TABLE `variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variations_product_id_foreign` (`product_id`),
  ADD KEY `variations_product_variation_id_foreign` (`product_variation_id`),
  ADD KEY `variations_name_index` (`name`),
  ADD KEY `variations_sub_sku_index` (`sub_sku`),
  ADD KEY `variations_variation_value_id_index` (`variation_value_id`),
  ADD KEY `variations_woocommerce_variation_id_index` (`woocommerce_variation_id`);

--
-- Indexes for table `variation_group_prices`
--
ALTER TABLE `variation_group_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variation_group_prices_variation_id_foreign` (`variation_id`),
  ADD KEY `variation_group_prices_price_group_id_foreign` (`price_group_id`);

--
-- Indexes for table `variation_location_details`
--
ALTER TABLE `variation_location_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variation_location_details_location_id_foreign` (`location_id`),
  ADD KEY `variation_location_details_product_id_index` (`product_id`),
  ADD KEY `variation_location_details_product_variation_id_index` (`product_variation_id`),
  ADD KEY `variation_location_details_variation_id_index` (`variation_id`);

--
-- Indexes for table `variation_templates`
--
ALTER TABLE `variation_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variation_templates_business_id_foreign` (`business_id`),
  ADD KEY `variation_templates_woocommerce_attr_id_index` (`woocommerce_attr_id`);

--
-- Indexes for table `variation_value_templates`
--
ALTER TABLE `variation_value_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variation_value_templates_name_index` (`name`),
  ADD KEY `variation_value_templates_variation_template_id_index` (`variation_template_id`);

--
-- Indexes for table `warranties`
--
ALTER TABLE `warranties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warranties_business_id_index` (`business_id`),
  ADD KEY `warranties_duration_type_index` (`duration_type`);

--
-- Indexes for table `woocommerce_sync_logs`
--
ALTER TABLE `woocommerce_sync_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_detail_types`
--
ALTER TABLE `account_detail_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `account_subtypes`
--
ALTER TABLE `account_subtypes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `account_transactions`
--
ALTER TABLE `account_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `aiassistance_history`
--
ALTER TABLE `aiassistance_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_maintenances`
--
ALTER TABLE `asset_maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_transactions`
--
ALTER TABLE `asset_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_warranties`
--
ALTER TABLE `asset_warranties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barcodes`
--
ALTER TABLE `barcodes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_capital`
--
ALTER TABLE `branch_capital`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business`
--
ALTER TABLE `business`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `business_locations`
--
ALTER TABLE `business_locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cash_denominations`
--
ALTER TABLE `cash_denominations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_registers`
--
ALTER TABLE `cash_registers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_register_transactions`
--
ALTER TABLE `cash_register_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms_pages`
--
ALTER TABLE `cms_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cms_page_metas`
--
ALTER TABLE `cms_page_metas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cms_site_details`
--
ALTER TABLE `cms_site_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `crm_call_logs`
--
ALTER TABLE `crm_call_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_campaigns`
--
ALTER TABLE `crm_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_contact_person_commissions`
--
ALTER TABLE `crm_contact_person_commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_lead_users`
--
ALTER TABLE `crm_lead_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_marketplaces`
--
ALTER TABLE `crm_marketplaces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_proposals`
--
ALTER TABLE `crm_proposals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_proposal_templates`
--
ALTER TABLE `crm_proposal_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_schedules`
--
ALTER TABLE `crm_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_schedule_logs`
--
ALTER TABLE `crm_schedule_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_schedule_users`
--
ALTER TABLE `crm_schedule_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `customer_groups`
--
ALTER TABLE `customer_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dashboard_configurations`
--
ALTER TABLE `dashboard_configurations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_and_notes`
--
ALTER TABLE `document_and_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_allowances_and_deductions`
--
ALTER TABLE `essentials_allowances_and_deductions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_attendances`
--
ALTER TABLE `essentials_attendances`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_documents`
--
ALTER TABLE `essentials_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_document_shares`
--
ALTER TABLE `essentials_document_shares`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_holidays`
--
ALTER TABLE `essentials_holidays`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_kb`
--
ALTER TABLE `essentials_kb`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_kb_users`
--
ALTER TABLE `essentials_kb_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_leaves`
--
ALTER TABLE `essentials_leaves`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_leave_types`
--
ALTER TABLE `essentials_leave_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_messages`
--
ALTER TABLE `essentials_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_payroll_groups`
--
ALTER TABLE `essentials_payroll_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_reminders`
--
ALTER TABLE `essentials_reminders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_shifts`
--
ALTER TABLE `essentials_shifts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_todo_comments`
--
ALTER TABLE `essentials_todo_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_to_dos`
--
ALTER TABLE `essentials_to_dos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_user_sales_targets`
--
ALTER TABLE `essentials_user_sales_targets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essentials_user_shifts`
--
ALTER TABLE `essentials_user_shifts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_layouts`
--
ALTER TABLE `invoice_layouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoice_schemes`
--
ALTER TABLE `invoice_schemes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mfg_ingredient_groups`
--
ALTER TABLE `mfg_ingredient_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mfg_recipes`
--
ALTER TABLE `mfg_recipes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mfg_recipe_ingredients`
--
ALTER TABLE `mfg_recipe_ingredients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=461;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_types`
--
ALTER TABLE `payment_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `pjt_invoice_lines`
--
ALTER TABLE `pjt_invoice_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pjt_projects`
--
ALTER TABLE `pjt_projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pjt_project_members`
--
ALTER TABLE `pjt_project_members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pjt_project_tasks`
--
ALTER TABLE `pjt_project_tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pjt_project_task_comments`
--
ALTER TABLE `pjt_project_task_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pjt_project_task_members`
--
ALTER TABLE `pjt_project_task_members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pjt_project_time_logs`
--
ALTER TABLE `pjt_project_time_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `printers`
--
ALTER TABLE `printers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_racks`
--
ALTER TABLE `product_racks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variations`
--
ALTER TABLE `product_variations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_lines`
--
ALTER TABLE `purchase_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reference_counts`
--
ALTER TABLE `reference_counts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `repair_device_models`
--
ALTER TABLE `repair_device_models`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repair_job_sheets`
--
ALTER TABLE `repair_job_sheets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repair_statuses`
--
ALTER TABLE `repair_statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `res_tables`
--
ALTER TABLE `res_tables`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `selling_price_groups`
--
ALTER TABLE `selling_price_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sheet_spreadsheets`
--
ALTER TABLE `sheet_spreadsheets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sheet_spreadsheet_shares`
--
ALTER TABLE `sheet_spreadsheet_shares`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustment_lines`
--
ALTER TABLE `stock_adjustment_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `superadmin_communicator_logs`
--
ALTER TABLE `superadmin_communicator_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `superadmin_frontend_pages`
--
ALTER TABLE `superadmin_frontend_pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system`
--
ALTER TABLE `system`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tax_rates`
--
ALTER TABLE `tax_rates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_sell_lines`
--
ALTER TABLE `transaction_sell_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_sell_lines_purchase_lines`
--
ALTER TABLE `transaction_sell_lines_purchase_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transfers`
--
ALTER TABLE `transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `types_of_services`
--
ALTER TABLE `types_of_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_contact_access`
--
ALTER TABLE `user_contact_access`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variations`
--
ALTER TABLE `variations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variation_group_prices`
--
ALTER TABLE `variation_group_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variation_location_details`
--
ALTER TABLE `variation_location_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variation_templates`
--
ALTER TABLE `variation_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variation_value_templates`
--
ALTER TABLE `variation_value_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warranties`
--
ALTER TABLE `warranties`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `woocommerce_sync_logs`
--
ALTER TABLE `woocommerce_sync_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `assets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `asset_transactions`
--
ALTER TABLE `asset_transactions`
  ADD CONSTRAINT `asset_transactions_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_transactions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `asset_transactions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `asset_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_transactions_receiver_foreign` FOREIGN KEY (`receiver`) REFERENCES `users` (`id`);

--
-- Constraints for table `barcodes`
--
ALTER TABLE `barcodes`
  ADD CONSTRAINT `barcodes_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `brands`
--
ALTER TABLE `brands`
  ADD CONSTRAINT `brands_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `brands_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `business`
--
ALTER TABLE `business`
  ADD CONSTRAINT `business_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  ADD CONSTRAINT `business_default_sales_tax_foreign` FOREIGN KEY (`default_sales_tax`) REFERENCES `tax_rates` (`id`),
  ADD CONSTRAINT `business_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `business_locations`
--
ALTER TABLE `business_locations`
  ADD CONSTRAINT `business_locations_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `business_locations_invoice_layout_id_foreign` FOREIGN KEY (`invoice_layout_id`) REFERENCES `invoice_layouts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `business_locations_invoice_scheme_id_foreign` FOREIGN KEY (`invoice_scheme_id`) REFERENCES `invoice_schemes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD CONSTRAINT `cash_registers_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cash_registers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_register_transactions`
--
ALTER TABLE `cash_register_transactions`
  ADD CONSTRAINT `cash_register_transactions_cash_register_id_foreign` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_registers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cms_page_metas`
--
ALTER TABLE `cms_page_metas`
  ADD CONSTRAINT `cms_page_metas_cms_page_id_foreign` FOREIGN KEY (`cms_page_id`) REFERENCES `cms_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contacts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_campaigns`
--
ALTER TABLE `crm_campaigns`
  ADD CONSTRAINT `crm_campaigns_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_lead_users`
--
ALTER TABLE `crm_lead_users`
  ADD CONSTRAINT `crm_lead_users_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_proposals`
--
ALTER TABLE `crm_proposals`
  ADD CONSTRAINT `crm_proposals_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_proposals_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_proposal_templates`
--
ALTER TABLE `crm_proposal_templates`
  ADD CONSTRAINT `crm_proposal_templates_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_schedules`
--
ALTER TABLE `crm_schedules`
  ADD CONSTRAINT `crm_schedules_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_schedule_logs`
--
ALTER TABLE `crm_schedule_logs`
  ADD CONSTRAINT `crm_schedule_logs_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `crm_schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_schedule_users`
--
ALTER TABLE `crm_schedule_users`
  ADD CONSTRAINT `crm_schedule_users_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `crm_schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_groups`
--
ALTER TABLE `customer_groups`
  ADD CONSTRAINT `customer_groups_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dashboard_configurations`
--
ALTER TABLE `dashboard_configurations`
  ADD CONSTRAINT `dashboard_configurations_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `essentials_kb`
--
ALTER TABLE `essentials_kb`
  ADD CONSTRAINT `essentials_kb_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `essentials_kb` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `essentials_payroll_group_transactions`
--
ALTER TABLE `essentials_payroll_group_transactions`
  ADD CONSTRAINT `essentials_payroll_group_transactions_payroll_group_id_foreign` FOREIGN KEY (`payroll_group_id`) REFERENCES `essentials_payroll_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `expense_categories_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_sub_taxes`
--
ALTER TABLE `group_sub_taxes`
  ADD CONSTRAINT `group_sub_taxes_group_tax_id_foreign` FOREIGN KEY (`group_tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_sub_taxes_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_layouts`
--
ALTER TABLE `invoice_layouts`
  ADD CONSTRAINT `invoice_layouts_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_schemes`
--
ALTER TABLE `invoice_schemes`
  ADD CONSTRAINT `invoice_schemes_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mfg_recipe_ingredients`
--
ALTER TABLE `mfg_recipe_ingredients`
  ADD CONSTRAINT `mfg_recipe_ingredients_mfg_recipe_id_foreign` FOREIGN KEY (`mfg_recipe_id`) REFERENCES `mfg_recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pjt_invoice_lines`
--
ALTER TABLE `pjt_invoice_lines`
  ADD CONSTRAINT `pjt_invoice_lines_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pjt_project_members`
--
ALTER TABLE `pjt_project_members`
  ADD CONSTRAINT `pjt_project_members_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pjt_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pjt_project_tasks`
--
ALTER TABLE `pjt_project_tasks`
  ADD CONSTRAINT `pjt_project_tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pjt_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pjt_project_task_comments`
--
ALTER TABLE `pjt_project_task_comments`
  ADD CONSTRAINT `pjt_project_task_comments_project_task_id_foreign` FOREIGN KEY (`project_task_id`) REFERENCES `pjt_project_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pjt_project_task_members`
--
ALTER TABLE `pjt_project_task_members`
  ADD CONSTRAINT `pjt_project_task_members_project_task_id_foreign` FOREIGN KEY (`project_task_id`) REFERENCES `pjt_project_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pjt_project_time_logs`
--
ALTER TABLE `pjt_project_time_logs`
  ADD CONSTRAINT `pjt_project_time_logs_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pjt_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pjt_project_time_logs_project_task_id_foreign` FOREIGN KEY (`project_task_id`) REFERENCES `pjt_project_tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `printers`
--
ALTER TABLE `printers`
  ADD CONSTRAINT `printers_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_repair_model_id_foreign` FOREIGN KEY (`repair_model_id`) REFERENCES `repair_device_models` (`id`),
  ADD CONSTRAINT `products_sub_category_id_foreign` FOREIGN KEY (`sub_category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_tax_foreign` FOREIGN KEY (`tax`) REFERENCES `tax_rates` (`id`),
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD CONSTRAINT `product_variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_lines`
--
ALTER TABLE `purchase_lines`
  ADD CONSTRAINT `purchase_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_lines_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_lines_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_lines_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repair_device_models`
--
ALTER TABLE `repair_device_models`
  ADD CONSTRAINT `repair_device_models_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `repair_device_models_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repair_device_models_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `repair_device_models_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `repair_job_sheets`
--
ALTER TABLE `repair_job_sheets`
  ADD CONSTRAINT `repair_job_sheets_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `repair_job_sheets_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repair_job_sheets_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repair_job_sheets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `repair_job_sheets_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `repair_job_sheets_device_model_id_foreign` FOREIGN KEY (`device_model_id`) REFERENCES `repair_device_models` (`id`),
  ADD CONSTRAINT `repair_job_sheets_service_staff_foreign` FOREIGN KEY (`service_staff`) REFERENCES `users` (`id`);

--
-- Constraints for table `res_product_modifier_sets`
--
ALTER TABLE `res_product_modifier_sets`
  ADD CONSTRAINT `res_product_modifier_sets_modifier_set_id_foreign` FOREIGN KEY (`modifier_set_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `res_tables`
--
ALTER TABLE `res_tables`
  ADD CONSTRAINT `res_tables_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `selling_price_groups`
--
ALTER TABLE `selling_price_groups`
  ADD CONSTRAINT `selling_price_groups_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sheet_spreadsheets`
--
ALTER TABLE `sheet_spreadsheets`
  ADD CONSTRAINT `sheet_spreadsheets_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sheet_spreadsheet_shares`
--
ALTER TABLE `sheet_spreadsheet_shares`
  ADD CONSTRAINT `sheet_spreadsheet_shares_sheet_spreadsheet_id_foreign` FOREIGN KEY (`sheet_spreadsheet_id`) REFERENCES `sheet_spreadsheets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_adjustment_lines`
--
ALTER TABLE `stock_adjustment_lines`
  ADD CONSTRAINT `stock_adjustment_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustment_lines_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustment_lines_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tax_rates`
--
ALTER TABLE `tax_rates`
  ADD CONSTRAINT `tax_rates_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tax_rates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_expense_for_foreign` FOREIGN KEY (`expense_for`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `business_locations` (`id`),
  ADD CONSTRAINT `transactions_pjt_project_id_foreign` FOREIGN KEY (`pjt_project_id`) REFERENCES `pjt_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_repair_job_sheet_id_foreign` FOREIGN KEY (`repair_job_sheet_id`) REFERENCES `repair_job_sheets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  ADD CONSTRAINT `transaction_payments_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_sell_lines`
--
ALTER TABLE `transaction_sell_lines`
  ADD CONSTRAINT `transaction_sell_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_sell_lines_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_sell_lines_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_sell_lines_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_crm_contact_id_foreign` FOREIGN KEY (`crm_contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variations`
--
ALTER TABLE `variations`
  ADD CONSTRAINT `variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variations_product_variation_id_foreign` FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variation_group_prices`
--
ALTER TABLE `variation_group_prices`
  ADD CONSTRAINT `variation_group_prices_price_group_id_foreign` FOREIGN KEY (`price_group_id`) REFERENCES `selling_price_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variation_group_prices_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variation_location_details`
--
ALTER TABLE `variation_location_details`
  ADD CONSTRAINT `variation_location_details_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `business_locations` (`id`),
  ADD CONSTRAINT `variation_location_details_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`);

--
-- Constraints for table `variation_templates`
--
ALTER TABLE `variation_templates`
  ADD CONSTRAINT `variation_templates_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variation_value_templates`
--
ALTER TABLE `variation_value_templates`
  ADD CONSTRAINT `variation_value_templates_variation_template_id_foreign` FOREIGN KEY (`variation_template_id`) REFERENCES `variation_templates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
